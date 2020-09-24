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
         * Initializing plugin installation.
         * 
         * @since 1.0.0
         */
        public function init()
        {
            $this->create_page();
            add_filter('display_post_states', [$this, 'add_display_status_on_page'], 10, 2);
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

        public function add_display_status_on_page($states, $post)
        {
            if(get_option('ea_wishlist_page_id') == $post->ID) {
                $post_status_object = get_post_status_object( $post->post_status );

                /* Checks if the label exists */
                if ( in_array( $post_status_object->name, $states, true ) ) {
                    return $states;
                }
                
                $states[ $post_status_object->name ] = __( 'Wishlist Page', 'wishlist' );
            }

            return $states;
        }

        public function is_installed()
        {
            return false;
        }
    }
}

function Wishlist_Install()
{
    return Wishlist_Install::get_instance();
}
