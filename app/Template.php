<?php

namespace BetterWishlist;

// If this file is called directly,  abort.
if (!defined('ABSPATH')) {
    die;
}

class Template
{
    public function __construct()
    {
        add_shortcode('better_wishlist_shortcode', [$this, 'better_wishlist_shortcode']);
    }

    public function better_wishlist_shortcode($atts, $content = null)
    {
        global $wpdb;

        $atts = shortcode_atts([
            'per_page' => 5,
            'current_page' => 1,
            'pagination' => 'no',
            'layout' => '',
        ], $atts);

        $items = Plugin::instance()->model->read_list(Plugin::instance()->model->get_current_user_list());
        $products = [];

        if ($items) {
            foreach ($items as $item) {
                $product = wc_get_product($item->product_id);

                if ($product) {
                    $products[] = [
                        'id' => $product->get_id(),
                        'title' => $product->get_title(),
                        'url' => get_permalink($product->get_id()),
                        'thumbnail_url' => get_the_post_thumbnail_url($product->get_id()),
                        'stock_status' => $product->get_stock_status(),
                    ];
                }
            }
        }

        return Plugin::instance()->twig->render('page.twig', ['ids' => wp_list_pluck($products, 'id'), 'products' => $products]);
    }
}
