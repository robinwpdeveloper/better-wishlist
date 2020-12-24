<?php

namespace BetterWishlist;

// If this file is called directly, abort.
if (!defined('ABSPATH')) {
    die;
}

class Plugin extends Singleton
{
    public $seed;
    public $schedule;
    public $helper;
    public $model;
    public $loader;
    public $twig;
    public $frontend;
    public $admin;

    protected function __construct()
    {
        // init modules
        $this->seed = new Seed;
        $this->schedule = new Schedule;
        $this->helper = new Helper;
        $this->model = new Model;
        $this->loader = new \Twig\Loader\FilesystemLoader(BETTER_WISHLIST_PLUGIN_PATH . 'public/views');
        $this->twig = new \Twig\Environment($this->loader);
        $this->frontend = new Frontend;

        if (is_admin()) {
            $this->admin = new Admin;
        }

        add_filter('body_class', [$this, 'add_body_class']);
        add_filter('display_post_states', [$this, 'add_display_status_on_page'], 10, 2);

        add_action('wp_ajax_add_to_wishlist', [$this, 'add_to_wishlist']);
        add_action('wp_ajax_nopriv_add_to_wishlist', [$this, 'add_to_wishlist']);

        add_action('wp_ajax_remove_from_wishlist', [$this, 'remove_from_wishlist']);
        add_action('wp_ajax_nopriv_remove_from_wishlist', [$this, 'remove_from_wishlist']);

        add_action('wp_ajax_add_to_cart_single', [$this, 'add_to_cart_single']);
        add_action('wp_ajax_nopriv_add_to_cart_single', [$this, 'add_to_cart_single']);

        add_action('wp_ajax_add_to_cart_multiple', [$this, 'add_to_cart_multiple']);
        add_action('wp_ajax_nopriv_add_to_cart_multiple', [$this, 'add_to_cart_multiple']);
    }

    public function add_display_status_on_page($states, $post)
    {
        if (get_option('better_wishlist_page_id') == $post->ID) {
            $post_status_object = get_post_status_object($post->post_status);

            /* Checks if the label exists */
            if (in_array($post_status_object->name, $states, true)) {
                return $states;
            }

            $states[$post_status_object->name] = __('Wishlist Page', 'better-wishlist');
        }

        return $states;
    }

    public function add_body_class($classes)
    {
        if (is_page() && get_the_ID() == get_option('better_wishlist_page_id')) {
            return array_merge($classes, ['woocommerce']);
        }
        return $classes;
    }

    public function add_to_wishlist()
    {
        check_ajax_referer('better_wishlist_nonce', 'security');

        if (empty($_REQUEST['product_id'])) {
            wp_send_json_error([
                'product_title' => '',
                'message' => __('Product ID is should not be empty.', 'better-wishlist'),
            ]);
        }

        $product_id = intval($_POST['product_id']);
        $wishlist_id = $this->model->get_current_user_list() ? $this->model->get_current_user_list() : $this->model->create_list();
        $already_in_wishlist = $this->model->item_in_list($product_id, $wishlist_id);

        if ($already_in_wishlist) {
            wp_send_json_error([
                'product_title' => get_the_title($product_id),
                'message' => __('already exists in wishlist.', 'better-wishlist'),
            ]);
        }

        // add to wishlist
        $this->model->insert_item($product_id, $wishlist_id);

        wp_send_json_success([
            'product_title' => get_the_title($product_id),
            'message' => __('added in wishlist.', 'better-wishlist'),
        ]);
    }

    public function remove_from_wishlist()
    {
        check_ajax_referer('better_wishlist_nonce', 'security');

        if (empty($_REQUEST['product_id'])) {
            wp_send_json_error([
                'product_title' => '',
                'message' => __('Product ID is should not be empty.', 'better-wishlist'),
            ]);
        }

        $product_id = intval($_POST['product_id']);
        $removed = Plugin::instance()->model->delete_item($product_id);

        if (!$removed) {
            wp_send_json_error([
                'product_title' => get_the_title($product_id),
                'message' => __('couldn\'t be removed.', 'better-wishlist'),
            ]);
        }

        wp_send_json_success([
            'product_title' => get_the_title($product_id),
            'message' => __('removed from wishlist.', 'better-wishlist'),
        ]);
    }

    public function add_to_cart_single()
    {
        check_ajax_referer('better_wishlist_nonce', 'security');

        if (empty($_REQUEST['product_id'])) {
            wp_send_json_error([
                'product_title' => '',
                'message' => __('Product ID is should not be empty.', 'better-wishlist'),
            ]);
        }

        $product_id = intval($_REQUEST['product_id']);
        $settings = get_option('bw_settings');

        if (WC()->cart->add_to_cart($product_id, 1)) {
            if ($settings['remove_from_wishlist']) {
                Plugin::instance()->model->delete_item($product_id);
            }

            wp_send_json_success([
                'product_title' => get_the_title($product_id),
                'message' => __('added in cart.', 'better-wishlist'),
            ]);
        }

        wp_send_json_error([
            'product_title' => get_the_title($product_id),
            'message' => __('couldn\'t be added in cart.', 'better-wishlist'),
        ]);
    }

    public function add_to_cart_multiple()
    {
        check_ajax_referer('better_wishlist_nonce', 'security');

        if (empty($_REQUEST['products'])) {
            wp_send_json_error([
                'product_title' => '',
                'message' => __('Product ID is should not be empty.', 'better-wishlist'),
            ]);
        }

        $settings = get_option('bw_settings');

        foreach ($_REQUEST['products'] as $product_id) {
            WC()->cart->add_to_cart($product_id, 1);

            if ($settings['remove_from_wishlist']) {
                Plugin::instance()->model->delete_item($product_id);
            }
        }

        wp_send_json_success([
            'product_title' => __('All items', 'better-wishlist'),
            'message' => __('added in cart.', 'better-wishlist'),
        ]);
    }
}
