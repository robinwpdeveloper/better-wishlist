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

        // create default page
        $this->create_page();

        // save default settings
        do_action('wprs_save_default_settings');
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

        $wpdb->better_wishlist_lists = $wpdb->prefix . 'better_wishlist_item_lists';
        $wpdb->better_wishlist_items = $wpdb->prefix . 'better_wishlist_item';
        $charset_collate = $wpdb->get_charset_collate();

        if ($wpdb->get_var("SHOW TABLES LIKE '$wpdb->better_wishlist_lists'") != $wpdb->better_wishlist_lists) {
            dbDelta("CREATE TABLE {$wpdb->better_wishlist_lists} (
                    ID BIGINT(20) NOT NULL AUTO_INCREMENT,
                    user_id BIGINT(20) NULL DEFAULT NULL,
                    session_id VARCHAR(255) DEFAULT NULL,
                    wishlist_slug VARCHAR(200) NOT NULL,
                    wishlist_name TEXT,
                    wishlist_token VARCHAR(64) NOT NULL UNIQUE,
                    wishlist_privacy TINYINT(1) NOT NULL DEFAULT 0,
                    is_default TINYINT(1) NOT NULL DEFAULT 0,
                    dateadded timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    expiration timestamp NULL DEFAULT NULL,
                    PRIMARY KEY  (ID),
                    KEY wishlist_slug (wishlist_slug),
                    KEY user_id (user_id)
               ) $charset_collate");
        }

        if ($wpdb->get_var("SHOW TABLES LIKE '$wpdb->better_wishlist_items'") != $wpdb->better_wishlist_items) {
            dbDelta("CREATE TABLE {$wpdb->better_wishlist_items} (
                    ID BIGINT(20) NOT NULL AUTO_INCREMENT,
                    product_id BIGINT(20) NOT NULL,
                    quantity INT(11) NOT NULL,
                    user_id BIGINT(20) NULL DEFAULT NULL,
                    wishlist_id BIGINT(20) NULL,
                    stock_status VARCHAR(64) DEFAULT NULL,
                    original_price DECIMAL(9, 3) NULL DEFAULT NULL,
                    original_currency CHAR(3) NULL DEFAULT NULL,
                    dateadded timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    on_sale tinyint NOT NULL DEFAULT 0,
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
        $post = get_post(get_option('better_wishlist_page_id'));

        if (empty($post)) {
            $post_id = wp_insert_post(array(
                'post_title' => __('Wishlist', 'better-wishlist'),
                'post_type' => 'page',
                'post_status' => 'publish',
                'post_author' => get_current_user_id(),
                'post_content' => '[better_wishlist_shortcode]',
            ));

            update_option('better_wishlist_page_id', $post_id);
        } else if ($post->post_status == 'trash') {
            wp_untrash_post($post->ID);
        } else if (in_array($post->post_status, ['pending', 'future', 'draft'])) {
            wp_publish_post($post->ID);
        }
    }

}
