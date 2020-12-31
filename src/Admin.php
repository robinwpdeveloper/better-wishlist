<?php

namespace BetterWishlist;

// If this file is called directly, abort.
if (!defined('ABSPATH')) {
    die;
}

class Admin
{
    public function __construct()
    {
        add_action('admin_menu', [$this, 'add_plugin_page'], 99);
    }

    public function add_plugin_page()
    {
        $page_hook_suffix = add_submenu_page(
            'woocommerce',
            __('BetterWishlist', 'better-wishlist'),
            __('BetterWishlist', 'better-wishlist'),
            'manage_options',
            'better-wishlist',
            [$this, 'create_admin_page'],
        );

        add_action("admin_print_scripts-{$page_hook_suffix}", [$this, 'enqueue_admin_scripts']);
    }

    public function create_admin_page()
    {
        echo '<div id="better-wishlist-admin"></div>';
    }

    public function enqueue_admin_scripts()
    {
        wp_enqueue_style('better-wishlist-admin-style', BETTER_WISHLIST_PLUGIN_URL . 'public/assets/css/admin.css', ['wp-components']);
        wp_enqueue_script('better-wishlist-admin-script', BETTER_WISHLIST_PLUGIN_URL . 'public/assets/js/admin.js', ['wp-data', 'wp-edit-post', 'wp-api', 'wp-i18n', 'wp-components', 'wp-element'], BETTER_WISHLIST_PLUGIN_VERSION, true);
    }

}
