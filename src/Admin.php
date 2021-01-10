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
        add_action('wp_ajax_bw_save_settings', [$this, 'save_settings']);
    }

    public function add_plugin_page()
    {
        $page_hook_suffix = add_submenu_page(
            'woocommerce',
            __('BetterWishlist', 'betterwishlist'),
            __('BetterWishlist', 'betterwishlist'),
            'manage_options',
            'betterwishlist',
            [$this, 'create_admin_page'],
        );

        add_action("admin_print_scripts-{$page_hook_suffix}", [$this, 'enqueue_admin_scripts']);
    }

    public function create_admin_page()
    {
        echo '<div id="betterwishlist-admin"></div>';
    }

    public function enqueue_admin_scripts()
    {
        wp_enqueue_style('betterwishlist-admin-style', BETTER_WISHLIST_PLUGIN_URL . 'public/assets/css/admin.css', ['wp-components']);
        wp_enqueue_script('betterwishlist-admin-script', BETTER_WISHLIST_PLUGIN_URL . 'public/assets/js/admin.js', ['wp-api', 'wp-api-fetch', 'wp-i18n', 'wp-components', 'wp-element'], BETTER_WISHLIST_PLUGIN_VERSION, true);
        wp_localize_script('betterwishlist-admin-script', 'BetterWishlist', [
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('betterwishlist'),
            'settings' => get_option('bw_settings'),
        ]);
    }

    public function save_settings()
    {
        check_ajax_referer('betterwishlist', 'security');

        $settings = array_map('sanitize_text_field', $_POST['settings']);
        $updated = update_option('bw_settings', $settings);

        if ($updated) {
            wp_send_json_success();
        }

        wp_send_json_error();
    }

}
