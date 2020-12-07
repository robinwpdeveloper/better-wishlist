<?php

if (!defined('ABSPATH')) {
    exit;
} // Exit if accessed directly

if (!class_exists('Better_Wishlist_Frontend')) {

    class Better_Wishlist_Frontend
    {

        /**
         * Single instance of the class
         *
         * @var \Better_Wishlist_Frontend
         * @since 1.0.0
         */
        protected static $instance;

        /**
         * Returns single instance of the class
         *
         * @return \Better_Wishlist_Frontend
         * @since 1.0.0
         */
        public static function get_instance()
        {
            if (is_null(self::$instance)) {
                self::$instance = new self();
            }

            return self::$instance;
        }

        public function __construct()
        {
            add_action('wp_enqueue_scripts', [$this, 'register_scripts']);

            // Add rewrite rules
            add_action('init', [$this, 'better_wishlist_page_endpoint']);
            add_filter('woocommerce_get_query_vars', [$this, 'better_wishlist_page_query_vars'], 0);
            add_action('after_switch_theme', [$this, 'better_wishlist_flush_rewrite_rules']);
            add_filter('woocommerce_account_menu_items', [$this, 'better_wishlist_add_menu']);

            add_action('woocommerce_account_better-wishlist_endpoint', array($this, 'better_wishlist_menu_content'));
        }

        public function register_scripts()
        {
            $localize_scripts = $this->get_localize();

            wp_register_script('jquery-wishlist-main', BETTER_WISHLIST_PLUGIN_URL . 'assets/js/' . 'jquery-better-wishlist.js', ['jquery'], '1.0.0', true);

            wp_register_style('wishlist-main-style', BETTER_WISHLIST_PLUGIN_URL . 'assets/css/' . 'better-wishlist.css', null, '1.0.0', 'all');

            wp_localize_script('jquery-wishlist-main', 'BETTER_WISHLIST_SCRIPTS', $localize_scripts);

            wp_enqueue_script('jquery-wishlist-main');

            wp_enqueue_style('wishlist-main-style');
        }

        /**
         * Return localize array
         *
         * @return array Array with variables to be localized inside js
         * @since 2.2.3
         */
        public function get_localize()
        {
            return apply_filters('better_wishlist_localize_script', [
                'ajax_url' => admin_url('admin-ajax.php', 'relative'),
                'nonce' => wp_create_nonce('better_wishlist_nonce'),
                'actions' => [
                    'add_to_wishlist_action' => 'add_to_wishlist',
                    'remove_from_wishlist_action' => 'remove_from_wishlist',
                    'multiple_product_add_to_cart_action' => 'mutiple_product_to_cart',
                    'single_product_add_to_cart_action' => 'single_product_to_cart',
                ],
            ]);
        }

        public function better_wishlist_page_endpoint()
        {
            add_rewrite_endpoint('better-wishlist', EP_ROOT | EP_PAGES);
        }

        public function better_wishlist_page_query_vars($vars)
        {
            $vars['better-wishlist'] = 'better-wishlist';

            return $vars;
        }

        public function better_wishlist_flush_rewrite_rules()
        {
            flush_rewrite_rules();
        }

        public function better_wishlist_add_menu($items)
        {

            $items = array_splice($items, 0, count($items) - 1) + array('better-wishlist' => __('Wishlist', 'better-wishlist')) + $items;
            return $items;
        }

        public function better_wishlist_menu_content()
        {
            echo do_shortcode('[better_wishlist_shortcode]');
        }

    }
}

Better_Wishlist_Frontend::get_instance();
