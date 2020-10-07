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


            add_action('init', ['Better_Wishlist_Form_Handler', 'remove_from_wishlist'], 20);
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
            if (isset($_GET['remove_from_wishlist'])) {
                $product_id = absint($_GET['remove_from_wishlist']);

                Better_Wishlist_Item()->remove($product_id);
            }
        }

        public static function mutiple_product_to_cart()
        {
            var_dump($_REQUEST);
        }
    }
}

Better_Wishlist_Form_Handler::init();
