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
        add_action('init', [$this, 'init']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts']);
        add_action('woocommerce_account_better-wishlist_endpoint', array($this, 'menu_content'));

        add_filter('woocommerce_account_menu_items', [$this, 'add_menu']);

        add_shortcode('better_wishlist', [$this, 'shortcode']);
    }

    public function init()
    {
        add_rewrite_endpoint('better-wishlist', EP_ROOT | EP_PAGES);

        // flush rewrite rules
        if (get_transient('better_wishlist_flush_rewrite_rules') === true) {
            flush_rewrite_rules();
            delete_transient('better_wishlist_flush_rewrite_rules');
        }
    }

    public function enqueue_scripts()
    {
        $localize_scripts = apply_filters('better_wishlist_localize_script', [
            'ajax_url' => admin_url('admin-ajax.php', 'relative'),
            'nonce' => wp_create_nonce('better_wishlist_nonce'),
            'actions' => [
                'add_to_wishlist_action' => 'add_to_wishlist',
                'remove_from_wishlist_action' => 'remove_from_wishlist',
                'multiple_product_add_to_cart_action' => 'mutiple_product_to_cart',
                'single_product_add_to_cart_action' => 'single_product_to_cart',
            ],
        ]);

        // css
        wp_enqueue_style('better-wishlist', BETTER_WISHLIST_PLUGIN_URL . 'public/assets/css/' . 'better-wishlist.css', null, '1.0.0', 'all');

        // js
        wp_enqueue_script('better-wishlist', BETTER_WISHLIST_PLUGIN_URL . 'public/assets/js/' . 'jquery-better-wishlist.js', ['jquery'], '1.0.0', true);
        wp_localize_script('better-wishlist', 'BETTER_WISHLIST_SCRIPTS', $localize_scripts);
    }

    public function add_menu($items)
    {
        $items = array_splice($items, 0, count($items) - 1) + ['better-wishlist' => __('Wishlist', 'better-wishlist')] + $items;

        return $items;
    }

    public function menu_content()
    {
        echo do_shortcode('[better_wishlist]');
    }

    public function shortcode($atts, $content = null)
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

    public function add_to_wishlist_button()
    {
        global $product;

        if (!$product) {
            return;
        }
    }
}
