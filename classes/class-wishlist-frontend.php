<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if( ! class_exists('Wishlist_Frontend') ) {

    class Wishlist_Frontend {

        protected static $instance;

		public static function get_instance(){
			if( is_null( self::$instance ) ){
				self::$instance = new self();
			}

			return self::$instance;
        }

        public function __construct()
        {
            add_action( 'wp_enqueue_scripts', [$this, 'register_scripts'] );
        }

        public function register_scripts()
        {
            $localize_scripts = $this->get_localize();

            wp_register_script( 'jquery-wishlist-main', Wishlist_PLUGIN_URL . 'assets/js/' . 'jquery-wishlist.js', ['jquery'], '1.0.0', true );
            wp_localize_script( 'jquery-wishlist-main', 'WISHLIST_SCRIPTS', $localize_scripts );

            wp_enqueue_script('jquery-wishlist-main');
        }
        
        /**
		 * Return localize array
		 *
		 * @return array Array with variables to be localized inside js
		 * @since 2.2.3
		 */
		public function get_localize() {
			return apply_filters( 'wishlist_wcwl_localize_script', array(
				'ajax_url' => admin_url( 'admin-ajax.php', 'relative' ),
				// 'redirect_to_cart' => get_option( 'wishlist_wcwl_redirect_cart' ),
				'multi_wishlist' => false,
				// 'hide_add_button' => apply_filters( 'wishlist_wcwl_hide_add_button', true ),
				// 'enable_ajax_loading' => 'yes' == get_option( 'wishlist_wcwl_ajax_enable', 'no' ),
				// 'ajax_loader_url' => Wishlist_PLUGIN_URL . 'assets/images/ajax-loader-alt.svg',
				// 'remove_from_wishlist_after_add_to_cart' => get_option( 'wishlist_wcwl_remove_after_add_to_cart' ) == 'yes',
				// 'is_wishlist_responsive' => apply_filters( 'wishlist_wcwl_is_wishlist_responsive', true ),
				// 'time_to_close_prettyphoto' => apply_filters( 'wishlist_wcwl_time_to_close_prettyphoto', 3000 ),
				// 'fragments_index_glue' => apply_filters( 'wishlist_wcwl_fragments_index_glue', '.' ),
				'labels' => array(
					'cookie_disabled' => __( 'We are sorry, but this feature is available only if cookies on your browser are enabled.', 'wishlist' ),
					'added_to_cart_message' => sprintf( '<div class="woocommerce-notices-wrapper"><div class="woocommerce-message" role="alert">%s</div></div>', apply_filters( 'wishlist_added_to_cart_message', __( 'Product added to cart successfully', 'wishlist' ) ) )
				),
				'actions' => array(
					'add_to_wishlist_action' => 'add_to_wishlist',
					'remove_from_wishlist_action' => 'remove_from_wishlist',
					'reload_wishlist_and_adding_elem_action'  => 'reload_wishlist_and_adding_elem',
					'load_mobile_action' => 'load_mobile',
					'delete_item_action' => 'delete_item',
					'save_title_action' => 'save_title',
					'save_privacy_action' => 'save_privacy',
					'load_fragments' => 'load_fragments'
				)
			) );
		}
        
    }

}

function Wishlist_Frontend() {
    return Wishlist_Frontend::get_instance();
}