<?php

namespace BetterWishlist;

// If this file is called directly,  abort.
if (!defined('ABSPATH')) {
    die;
}

class Seed
{
    public function __construct()
    {
        register_activation_hook(BETTER_WISHLIST_PLUGIN_FILE, [$this, 'run']);
    }

    public function run()
    {
        // create wishlist tables
        $this->create_tables();

        // save default settings
        if (get_option('better_wishlist_settings') === false) {
            update_option('bw_settings', [
                'wishlist_page' => null,
                'wishlist_menu' => 'yes',
                'redirect_to_wishlist' => 'no',
                'redirect_to_cart' => 'no',
                'remove_from_wishlist' => 'yes',
                'show_in_loop' => 'yes',
                'position_in_loop' => 'after_cart',
                'position_in_single' => 'after_cart',
                'add_to_wishlist_text' => __('Add to wishlist', 'betterwishlist'),
                'add_to_cart_text' => __('Add to cart', 'betterwishlist'),
                'add_all_to_cart_text' => __('Add all to cart', 'betterwishlist'),
                'wishlist_button_style' => 'default',
                'wishlist_button_color' => '#ffffff',
                'wishlist_button_background' => 'default',
                'wishlist_button_hover_color' => 'default',
                'wishlist_button_hover_background' => 'default',
                'wishlist_button_border_style' => 'none',
                'wishlist_button_border_width' => 1,
                'wishlist_button_border_color' => 'default',
                'wishlist_button_padding_top' => 0,
                'wishlist_button_padding_right' => 0,
                'wishlist_button_padding_bottom' => 0,
                'wishlist_button_padding_left' => 0,
                'cart_button_style' => 'default',
                'cart_button_color' => '#ffffff',
                'cart_button_background' => 'default',
                'cart_button_hover_color' => 'default',
                'cart_button_hover_background' => 'default',
                'cart_button_border_style' => 'none',
                'cart_button_border_width' => 1,
                'cart_button_border_color' => 'default',
                'cart_button_padding_top' => 0,
                'cart_button_padding_right' => 0,
                'cart_button_padding_bottom' => 0,
                'cart_button_padding_left' => 0,
            ]);

            // set flush rewrite flag enabled
            set_transient('better_wishlist_flush_rewrite_rules', true, 86400);

            // create default page
            $this->create_page();
        }
    }

    /**
     * Add database tables.
     *
     * @return void
     * @access public
     * @since  1.0.0
     */
    public function create_tables()
    {
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        global $wpdb;

        $better_wishlist_lists = $wpdb->prefix . 'better_wishlist_lists';
        $better_wishlist_items = $wpdb->prefix . 'better_wishlist_items';
        $charset_collate = $wpdb->get_charset_collate();

        if ($wpdb->get_var("SHOW TABLES LIKE '$better_wishlist_lists'") != $better_wishlist_lists) {
            dbDelta("CREATE TABLE $better_wishlist_lists (
                    ID BIGINT(20) NOT NULL AUTO_INCREMENT,
                    user_id BIGINT(20) NULL DEFAULT NULL,
                    session_id VARCHAR(255) DEFAULT NULL,
                    wishlist_slug VARCHAR(200) NOT NULL,
                    wishlist_name TEXT,
                    wishlist_token VARCHAR(64) NOT NULL UNIQUE,
                    wishlist_privacy TINYINT(1) NOT NULL DEFAULT 0,
                    is_default TINYINT(1) NOT NULL DEFAULT 0,
                    created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    expire_on timestamp NULL DEFAULT NULL,
                    PRIMARY KEY  (ID),
                    KEY wishlist_slug (wishlist_slug),
                    KEY user_id (user_id)
               ) $charset_collate");
        }

        if ($wpdb->get_var("SHOW TABLES LIKE '$better_wishlist_items'") != $better_wishlist_items) {
            dbDelta("CREATE TABLE $better_wishlist_items (
                    ID BIGINT(20) NOT NULL AUTO_INCREMENT,
                    product_id BIGINT(20) NOT NULL,
                    user_id BIGINT(20) NULL DEFAULT NULL,
                    wishlist_id BIGINT(20) NULL,
                    created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    PRIMARY KEY  (ID),
                    KEY product_id (product_id)
               ) $charset_collate");
        }
    }

    /**
     * Add "Wishlist" page in database.
     *
     * @return void
     * @access public
     * @since  1.0.0
     */
    public function create_page()
    {
        $settings = get_option('bw_settings');
        $post = get_post($settings['wishlist_page']);

        if (empty($post)) {
            $post_id = wp_insert_post(array(
                'post_title' => __('Wishlist', 'betterwishlist'),
                'post_type' => 'page',
                'post_status' => 'publish',
                'post_author' => get_current_user_id(),
                'post_content' => '[better_wishlist]',
            ));

            $settings['wishlist_page'] = $post_id;
            update_option('bw_settings', $settings);
        } else if ($post->post_status == 'trash') {
            wp_untrash_post($post->ID);
        } else if (in_array($post->post_status, ['pending', 'future', 'draft'])) {
            wp_publish_post($post->ID);
        }
    }

}
