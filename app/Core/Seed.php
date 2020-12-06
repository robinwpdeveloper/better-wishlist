<?php

namespace BetterWishlist\Core;

// If this file is called directly, abort.
if (!defined('ABSPATH')) {
    die;
}

class Plugin extends Singleton
{
    private function __construct()
    {
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        $this->create_tables();
    }

    /**
     * Add tables for first installation.
     *
     * @return void
     * @access private
     * @since  1.0.0
     */
    private function create_tables()
    {
        global $wpdb;

        $wpdb->better_wishlist_items = $wpdb->prefix . 'better_wishlist_item';
        $wpdb->better_wishlist_lists = $wpdb->prefix . 'better_wishlist_item_lists';

        $charset_collate = $wpdb->get_charset_collate();

        if ($wpdb->get_var("SHOW TABLES LIKE '$wpdb->better_wishlist_lists'") != $wpdb->better_wishlist_lists) {
            dbDelta("CREATE TABLE {$wpdb->better_wishlist_lists} (
                    ID BIGINT( 20 ) NOT NULL AUTO_INCREMENT,
                    user_id BIGINT( 20 ) NULL DEFAULT NULL,
                    session_id VARCHAR( 255 ) DEFAULT NULL,
                    wishlist_slug VARCHAR( 200 ) NOT NULL,
                    wishlist_name TEXT,
                    wishlist_token VARCHAR( 64 ) NOT NULL UNIQUE,
                    wishlist_privacy TINYINT( 1 ) NOT NULL DEFAULT 0,
                    is_default TINYINT( 1 ) NOT NULL DEFAULT 0,
                    dateadded timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    expiration timestamp NULL DEFAULT NULL,
                    PRIMARY KEY  ( ID ),
                    KEY wishlist_slug ( wishlist_slug ),
                    KEY user_id ( user_id )
                ) $charset_collate");
        }

        if ($wpdb->get_var("SHOW TABLES LIKE '$wpdb->better_wishlist_items'") != $wpdb->better_wishlist_items) {
            dbDelta("CREATE TABLE {$wpdb->better_wishlist_items} (
                    ID BIGINT( 20 ) NOT NULL AUTO_INCREMENT,
                    product_id BIGINT( 20 ) NOT NULL,
                    quantity INT( 11 ) NOT NULL,
                    user_id BIGINT( 20 ) NULL DEFAULT NULL,
                    wishlist_id BIGINT( 20 ) NULL,
                    stock_status VARCHAR( 64 ) DEFAULT NULL,
                    original_price DECIMAL( 9,3 ) NULL DEFAULT NULL,
                    original_currency CHAR( 3 ) NULL DEFAULT NULL,
                    dateadded timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    on_sale tinyint NOT NULL DEFAULT 0,
                    PRIMARY KEY  ( ID ),
                    KEY product_id ( product_id )
                ) $charset_collate");
        }
    }

}
