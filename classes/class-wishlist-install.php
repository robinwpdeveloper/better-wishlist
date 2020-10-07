<?php

/**
 * Install file
 *
 * @since             1.0.0
 * @package           Better_Wishlist\Better_Wishlist_Install
 */

// If this file is called directly, abort.
if (!defined('ABSPATH')) {
    die;
}

if (!class_exists('Better_Wishlist_Install')) {

    /**
     * Creating plugin table and wishlist page.
     *
     * @since 1.0.0
     */
    class Better_Wishlist_Install {

        /**
         * Initializing plugin installation.
         *
         * @since 1.0.0
         */
        public static function install () {
            self::create_tables();
            self::create_page();
        }

        /**
         * Add tables for first installation.
         *
         * @return void
         * @access private
         * @since  1.0.0
         */
        private static function create_tables () {
            global $wpdb;
            require_once(ABSPATH.'wp-admin/includes/upgrade.php');

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
                ) DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");
            }

            if ($wpdb->get_var("SHOW TABLES LIKE '$wpdb->better_wishlist_items'") != $wpdb->better_wishlist_items) {
                dbDelta("CREATE TABLE {$wpdb->better_wishlist_items} (
                    ID BIGINT( 20 ) NOT NULL AUTO_INCREMENT,
                    product_id BIGINT( 20 ) NOT NULL,
                    quantity INT( 11 ) NOT NULL,
                    user_id BIGINT( 20 ) NULL DEFAULT NULL,
                    wishlist_id BIGINT( 20 ) NULL,
                    position INT( 11 ) DEFAULT 0,
                    original_price DECIMAL( 9,3 ) NULL DEFAULT NULL,
                    original_currency CHAR( 3 ) NULL DEFAULT NULL,
                    dateadded timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    on_sale tinyint NOT NULL DEFAULT 0,
                    PRIMARY KEY  ( ID ),
                    KEY product_id ( product_id )
                ) DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");
            }
        }

        /**
         * Add a page "Wishlist" on the database.
         *
         * @since 1.0.0
         */
        public static function create_page () {
            Better_Wishlist_Helper::better_wishlist_create_page(
                sanitize_title_with_dashes(_x('better-wishlist', 'page_slug', 'ea-woocommerce-wishlist')),
                'better_wishlist_page_id',
                __('Wishlist', 'better-wishlist'),
                '[better_wishlist_shortcode]'
            );
        }
    }
}
