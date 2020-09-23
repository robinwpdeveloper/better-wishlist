<?php
/**
 * Shortcodes class
 *
 * @author Your Inspiration Themes
 * @package YITH WooCommerce Wishlist
 * @version 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class Wishlist_Shortcode {

    public static function init() {
        add_shortcode( 'wishlist_add_to_wishlist_button', array( __CLASS__, 'add_to_wishlist' ) );
    }

    public static function add_to_wishlist($atts, $content = null) {
        global $product;

        $current_product = ( isset( $atts['product_id'] ) ? wc_get_product( $atts['product_id'] ) : false );
        $current_product = $current_product ? $current_product : $product;

        if( ! $current_product ) {
            return '';
        }
        
        
    }



}