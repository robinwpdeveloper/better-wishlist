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

        // set flush rewrite flag enabled
        set_transient('better_wishlist_flush_rewrite_rules', true, 86400);

        // save default settings
        if (get_option('better_wishlist_settings') === false) {
            update_option('better_wishlist_settings', [
                'remove_from_wishlist' => 'remove_from_wishlist',
            ]);
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
        $post = get_post(get_option('better_wishlist_page_id'));

        if (empty($post)) {
            $post_id = wp_insert_post(array(
                'post_title' => __('Wishlist', 'betterwishlist'),
                'post_type' => 'page',
                'post_status' => 'publish',
                'post_author' => get_current_user_id(),
                'post_content' => '[better_wishlist]',
            ));

            update_option('better_wishlist_page_id', $post_id);
        } else if ($post->post_status == 'trash') {
            wp_untrash_post($post->ID);
        } else if (in_array($post->post_status, ['pending', 'future', 'draft'])) {
            wp_publish_post($post->ID);
        }
    }

}
