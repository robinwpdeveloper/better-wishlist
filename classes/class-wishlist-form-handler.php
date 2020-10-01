<?php

if (!defined('ABSPATH')) {
    exit;
} // Exit if accessed directly

if (!class_exists('Wishlist_Form_Handler')) {

    class Wishlist_Form_Handler
    {

        public static function init()
        {
            if (!self::process_form_handling()) {
                return;
            }

            add_action('wp_ajax_add_to_wishlist', array('Wishlist_Form_Handler', 'add_to_wishlist'));
            add_action('wp_ajax_nopriv_add_to_wishlist', array('Wishlist_Form_Handler', 'add_to_wishlist'));
        }

        public static function process_form_handling()
        {
            $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? sanitize_text_field(wp_unslash($_SERVER['HTTP_USER_AGENT'])) : false;

            if ($user_agent && apply_filters('wishlist_wcwl_block_user_agent', preg_match('/bot|crawl|slurp|spider|wordpress/i', $user_agent), $user_agent)) {
                return false;
            }

            return true;
        }

        public static function add_to_wishlist()
        {

            $wishlist_id = User_Wishlist()->get_current_user_wishlist() ? User_Wishlist()->get_current_user_wishlist() : User_Wishlist()->create();
            $added_to_wishlist = Wishlist_Item()->add($_REQUEST['fragments'], $wishlist_id);

            die();
        }

    }
}

Wishlist_Form_Handler::init();
