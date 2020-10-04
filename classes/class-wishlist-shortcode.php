<?php

/**
 * Add to wishlists shortcode and hooks
 *
 * @since             1.0.0
 * @package           Wishlist\Classes
 */

// If this file is called directly, abort.
if (!defined('ABSPATH')) {
    die;
}

if (!class_exists('Wishlist_Shortcode')) {
    class Wishlist_Shortcode
    {

        /**
         * This class
         *
         * @var \Wishlist
         */
        protected static $instance = null;

        /**
         * Get this class object
         *
         * @param string $plugin_name Plugin name.
         *
         * @return \Wishlist
         */
        public static function get_instance() {
            if ( is_null( self::$instance ) ) {
                self::$instance = new self();
            }

            return self::$instance;
        }

        public function __construct()
        {
            add_shortcode('wishlist_shortcode', [$this, 'wishlist']);
        }


        public function wishlist($atts, $content = null)
        {
            $atts = shortcode_atts([
                'per_page'  => 5,
                'current_page'  => 1,
                'pagination'    => 'no',
                'layout'    => ''
            ], $atts);

            extract($atts);

            $items = Wishlist_Item()->get_items(User_Wishlist()->get_current_user_wishlist());
            


        }
    }
}

Wishlist_Shortcode::get_instance();