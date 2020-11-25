<?php

if (!defined('ABSPATH')) {
    exit;
} // Exit if accessed directly

if (!class_exists('Better_Wishlist_Form_Handler')) {

    class Better_Wishlist_Form_Handler
    {

        public static function init()
        {
            if (!self::process_form_handling()) {
                return;
            }

            add_action('wp_ajax_add_to_wishlist', ['Better_Wishlist_Form_Handler', 'add_to_wishlist']);
            add_action('wp_ajax_nopriv_add_to_wishlist', ['Better_Wishlist_Form_Handler', 'add_to_wishlist']);

            add_action('wp_ajax_mutiple_product_to_cart', ['Better_Wishlist_Form_Handler', 'mutiple_product_to_cart']);
            add_action('wp_ajax_nopriv_mutiple_product_to_cart', ['Better_Wishlist_Form_Handler', 'mutiple_product_to_cart']);

            add_action('wp_ajax_remove_from_wishlist', ['Better_Wishlist_Form_Handler', 'remove_from_wishlist']);
            add_action('wp_ajax_nopriv_remove_from_wishlist', ['Better_Wishlist_Form_Handler', 'remove_from_wishlist']);

            add_action('wp_ajax_single_product_to_cart', ['Better_Wishlist_Form_Handler', 'single_product_to_cart']);
            add_action('wp_ajax_nopriv_single_product_to_cart', ['Better_Wishlist_Form_Handler', 'single_product_to_cart']);

        }

        public static function process_form_handling()
        {
            $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? sanitize_text_field(wp_unslash($_SERVER['HTTP_USER_AGENT'])) : false;

            if ($user_agent && apply_filters('better_wishlist_wcwl_block_user_agent', preg_match('/bot|crawl|slurp|spider|wordpress/i', $user_agent), $user_agent)) {
                return false;
            }

            return true;
        }

        public static function add_to_wishlist()
        {
            check_ajax_referer( 'better_wishlist_nonce' );
            $wishlist_id = User_Wishlist()->get_current_user_wishlist() ? User_Wishlist()->get_current_user_wishlist() : User_Wishlist()->create();

            $product_id = self::get_proudct_id($_REQUEST['fragments']);
            
            $is_already_in_wishlist = Better_Wishlist_Item()->is_already_in_wishlist($product_id, $wishlist_id);

            
            if(!$is_already_in_wishlist) {
                $added_to_wishlist = Better_Wishlist_Item()->add($_REQUEST['fragments'], $wishlist_id);
                wp_send_json_success($added_to_wishlist, 200);
            }else {
                wp_send_json_error(['message' => __( 'Already in wishlist', 'better-wishlist')]);
            }

            die();
        }

        public static function get_proudct_id($fragments) 
        {
            return isset( $fragments['product_id']) ? $fragments['product_id'] : false;
        }

        public static function remove_from_wishlist()
        {
            if (!empty($_REQUEST['product_id'])) {
                $product_id = absint($_REQUEST['product_id']);

                Better_Wishlist_Item()->remove($product_id);
            }
        }

        public static function mutiple_product_to_cart()
        {
            check_ajax_referer( 'better_wishlist_nonce' );
            if( empty($_REQUEST['product_ids']) ) {
                return false;
            }
            $product_ids = apply_filters('better_wishlist_multiple_product_ids_to_add_to_cart', $_REQUEST['product_ids']);
            foreach($product_ids as $id) {

                $product = wc_get_product($id);
                WC()->cart->add_to_cart( $id, 1);

                if( Better_Wishlist_Helper::get_settings('remove_from_wishlist')){
                  $removed = Better_Wishlist_Item()->remove($id);
                } else {
                  $removed = false;
                }
            }
            if( Better_Wishlist_Helper::get_settings('cart_page_redirect')){
                 wp_safe_redirect( wc_get_cart_url() );
                 exit;
            }
            wp_send_json_success($removed);
        }

        public static function single_product_to_cart()
        {
            check_ajax_referer( 'better_wishlist_nonce' );
            if( empty($_REQUEST['product_id']) ) {
                return false;
            }
          
            $product_id = intval($_REQUEST['product_id']);

            WC()->cart->add_to_cart( $product_id, 1);

            $data = array(
              'removed' => false,
              'redirects' => null
            );
            
            if( Better_Wishlist_Helper::get_settings('remove_from_wishlist')){
              Better_Wishlist_Item()->remove($product_id,false);
              $data['removed'] = true;
            }      

            if( Better_Wishlist_Helper::get_settings('cart_page_redirect')){
              $data['redirects'] = wc_get_cart_url();
            }

            wp_send_json_success($data);

        }
    }
}

Better_Wishlist_Form_Handler::init();
