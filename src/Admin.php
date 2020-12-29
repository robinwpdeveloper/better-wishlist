<?php

namespace BetterWishlist;

// If this file is called directly, abort.
if (!defined('ABSPATH')) {
    die;
}

class Admin
{
    private $settings;

    public function __construct()
    {
        add_action('admin_menu', [$this, 'add_plugin_page'], 99);
        // add_action('admin_init', [$this, 'init_page']);
    }

    public function add_plugin_page()
    {
        $page_hook_suffix = add_submenu_page(
            'woocommerce', // parent
            __('Better Wishlist', 'better-wishlist'), // page_title
            __('Better Wishlist', 'better-wishlist'), // menu_title
            'manage_options', // capability
            'better-wishlist', // menu_slug
            [$this, 'create_admin_page'], // function
        );

        add_action("admin_print_scripts-{$page_hook_suffix}", [$this, 'enqueue_admin_scripts']);
    }

    public function create_admin_page()
    {
        echo '<div id="better-wishlist-admin"></div>';
    }

    public function enqueue_admin_scripts()
    {
        wp_enqueue_style('better-wishlist-admin-style', BETTER_WISHLIST_PLUGIN_URL . 'public/assets/css/admin.css', array('wp-components'));
        wp_enqueue_script('better-wishlist-admin-script', BETTER_WISHLIST_PLUGIN_URL . 'public/assets/js/admin.js', array('wp-api', 'wp-i18n', 'wp-components', 'wp-element'), BETTER_WISHLIST_PLUGIN_VERSION, true);
    }

    // public function init_page()
    // {
    //     register_setting(
    //         'better_wishlist_option_group', // option_group
    //         'better_wishlist_settings', // option_name
    //     );

    //     add_settings_section(
    //         'better_wishlist_setting_section', // id
    //         'Settings', // title
    //         false, // callback
    //         'better-wishlist-admin' // page
    //     );

    //     add_settings_field(
    //         'redirect_to_wishlist', // id
    //         'Redirect to wishlist', // title
    //         [$this, 'redirect_to_wishlist_callback'], // callback
    //         'better-wishlist-admin', // page
    //         'better_wishlist_setting_section' // section
    //     );

    //     add_settings_field(
    //         'remove_from_wishlist', // id
    //         'Remove from wishlist', // title
    //         [$this, 'remove_from_wishlist_callback'], // callback
    //         'better-wishlist-admin', // page
    //         'better_wishlist_setting_section' // section
    //     );

    //     add_settings_field(
    //         'redirect_to_cart', // id
    //         'Redirect to cart', // title
    //         [$this, 'redirect_to_cart_callback'], // callback
    //         'better-wishlist-admin', // page
    //         'better_wishlist_setting_section' // section
    //     );
    // }

    // public function redirect_to_wishlist_callback()
    // {
    //     printf(
    //         '<input type="checkbox" name="better_wishlist_settings[redirect_to_wishlist]" id="redirect_to_wishlist" value="redirect_to_wishlist" %s> <label for="redirect_to_wishlist">Redirect to wishlist page after adding a product to wishlist</label>',
    //         (isset($this->settings['redirect_to_wishlist']) && $this->settings['redirect_to_wishlist'] === 'redirect_to_wishlist') ? 'checked' : ''
    //     );
    // }

    // public function remove_from_wishlist_callback()
    // {
    //     printf(
    //         '<input type="checkbox" name="better_wishlist_settings[remove_from_wishlist]" id="remove_from_wishlist" value="remove_from_wishlist" %s> <label for="remove_from_wishlist">Remove from wishlist after adding a product to cart</label>',
    //         (isset($this->settings['remove_from_wishlist']) && $this->settings['remove_from_wishlist'] === 'remove_from_wishlist') ? 'checked' : ''
    //     );
    // }

    // public function redirect_to_cart_callback()
    // {
    //     printf(
    //         '<input type="checkbox" name="better_wishlist_settings[redirect_to_cart]" id="redirect_to_cart" value="redirect_to_cart" %s> <label for="redirect_to_cart">Redirect to cart page after adding a product to cart</label>',
    //         (isset($this->settings['redirect_to_cart']) && $this->settings['redirect_to_cart'] === 'redirect_to_cart') ? 'checked' : ''
    //     );
    // }

}
