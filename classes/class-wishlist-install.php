<?php

/**
 * Install file
 *
 * @since             1.0.0
 * @package           Wishlist\Wishlist_Install
 */

// If this file is called directly, abort.
if (!defined('ABSPATH')) {
    die;
}

if (!class_exists('Wishlist_Install')) {

    /**
     * Creating plugin table and wishlist page.
     * 
     * @since 1.0.0
     */
    class Wishlist_Install
    {

        /**
         * Single instance of the class
         *
         * @var \Wishlist_Install
         * @since 1.0.0
         */
        protected static $instance;

        /**
         * Wishlist Items table name.
         * 
         * @var string
         * @access private
         * @since 1.0.0
         */
        private $items_table;

        /**
         * Wishlist table name.
         * 
         * @var string
         * @access private
         * @since   1.0.0
         */
        private $wishlist_wishlists_table;

        /**
         * Returns single instance of the class
         *
         * @return \Wishlist_Install
         * @since 1.0.0
         */
        public static function get_instance()
        {
            if (is_null(self::$instance)) {
                self::$instance = new self();
            }

            return self::$instance;
        }

        /**
         * \Wishlist_Install Constructor
         * 
         * @since 1.0.0
         */
        public function __construct()
        {
            global $wpdb;

            $this->items_table = $wpdb->prefix . 'wishlist_item';
            $this->wishlist_wishlists_table = $wpdb->prefix . 'wishlist_item_lists';

            $wpdb->ea_wishlist_items = $this->items_table;
            $wpdb->ea_wishlist_lists = $this->wishlist_wishlists_table;

            // define constant to use in entire the application.
            define( 'WISHLIST_ITEMS_TABLE', $this->items_table );
            define( 'WISHLIST_WISHLISTS_TABLE', $this->wishlist_wishlists_table );

        }

        /**
         * Initializing plugin installation.
         * 
         * @since 1.0.0
         */
        public function init()
        {
            $this->create_tables();
            $this->create_page();
        }

        /**
         * Add tables for first installation.
         * 
         * @return void
         * @access private
         * @since 1.0.0
         */
        private function create_tables()
        {
            global $wpdb;
            require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

            if($wpdb->get_var("SHOW TABLES LIKE '$this->wishlist_wishlists_table'") != $this->wishlist_wishlists_table) {
                dbDelta("CREATE TABLE {$this->wishlist_wishlists_table} (
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

            if( $wpdb->get_var("SHOW TABLES LIKE '$this->items_table'") != $this->items_table ) {
                dbDelta("CREATE TABLE {$this->items_table} (
                    ID BIGINT( 20 ) NOT NULL AUTO_INCREMENT,
                    prod_id BIGINT( 20 ) NOT NULL,
                    quantity INT( 11 ) NOT NULL,
                    user_id BIGINT( 20 ) NULL DEFAULT NULL,
                    wishlist_id BIGINT( 20 ) NULL,
                    position INT( 11 ) DEFAULT 0,
                    original_price DECIMAL( 9,3 ) NULL DEFAULT NULL,
                    original_currency CHAR( 3 ) NULL DEFAULT NULL,
                    dateadded timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    on_sale tinyint NOT NULL DEFAULT 0,
                    PRIMARY KEY  ( ID ),
                    KEY prod_id ( prod_id )
                ) DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");
            }
        }

        /**
         * Add a page "Wishlist" on the database.
         * 
         * @since 1.0.0
         */
        private function create_page()
        {
            if (!function_exists('wc_create_page')) {
                require_once ABSPATH . 'wp-content/plugins/woocommerce/includes/admin/wc-admin-functions.php';
            }

            wc_create_page(
                sanitize_title_with_dashes(_x('ea_wishlist', 'page_slug', 'ea-woocommerce-wishlist')),
                'ea_wishlist_page_id',
                __('Wishlist', 'wishlist')
            );
        }

        /**
         * Check if the table of the plugin is already exists.
         * 
         * @return bool
         * @since 1.0.0
         */
        public function is_installed()
        {
            global $wpdb;
            $number_of_tables = $wpdb->query( $wpdb->prepare( 'SHOW TABLES LIKE %s', "{$this->items_table}" ) );
            
            return (bool) ( 2 == $number_of_tables );
        }

    }
}

/**
 * Onetime access to instance of Wishlist_Install class
 *
 * @return \Wishlist_Install
 * @since 1.0.0
 */
function Wishlist_Install()
{
    return Wishlist_Install::get_instance();
}
