<?php

namespace BetterWishlist;

// If this file is called directly, abort.
if (!defined('ABSPATH')) {
    die;
}

class Frontend
{
    public function __construct()
    {
        add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts']);
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
}
