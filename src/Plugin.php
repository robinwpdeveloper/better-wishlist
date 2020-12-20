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

    protected function __construct()
    {
        // init modules
        $this->seed = new Seed;
        $this->schedule = new Schedule;
        $this->helper = new Helper;
        $this->model = new Model;
        $this->loader = new \Twig\Loader\FilesystemLoader(BETTER_WISHLIST_PLUGIN_PATH . 'public/views');
        $this->twig = new \Twig\Environment($this->loader);
        $this->frontend = new Frontend();

        add_filter('body_class', [$this, 'add_body_class']);
        add_filter('display_post_states', [$this, 'add_display_status_on_page'], 10, 2);

        add_action('wp_ajax_add_to_wishlist', [$this, 'add_to_wishlist']);
        add_action('wp_ajax_nopriv_add_to_wishlist', [$this, 'add_to_wishlist']);

        add_action('wp_ajax_mutiple_product_to_cart', [$this, 'mutiple_product_to_cart']);
        add_action('wp_ajax_nopriv_mutiple_product_to_cart', [$this, 'mutiple_product_to_cart']);

        add_action('wp_ajax_remove_from_wishlist', [$this, 'remove_from_wishlist']);
        add_action('wp_ajax_nopriv_remove_from_wishlist', [$this, 'remove_from_wishlist']);

        add_action('wp_ajax_single_product_to_cart', [$this, 'single_product_to_cart']);
        add_action('wp_ajax_nopriv_single_product_to_cart', [$this, 'single_product_to_cart']);

        add_action('wprs_build_settings', function ($config) {
            $config::add_tab([
                'title' => __('General Settings', 'better-wishlist'),
                'id' => 'general_settings',
            ]);

            $config::add_field('general_settings', [
                'id' => 'add_to_wishlist_text',
                'type' => 'text',
                'title' => __('Add to wishlist button text', 'better-wishlist'),
                'default' => 'Add to wishlist',
            ]);

            $config::add_field('general_settings', [
                'id' => 'added_to_wishlist_text',
                'type' => 'text',
                'title' => __('"Product added to Wishlist" Text', 'better-wishlist'),
                'default' => 'Added to Wishlist',
            ]);

            $config::add_field('general_settings', [
                'id' => 'already_in_wishlist',
                'type' => 'text',
                'title' => __('"Product already in Wishlist" Text', 'better-wishlist'),
                'default' => 'Already in Wishlist',
            ]);

            $config::add_field('general_settings', [
                'id' => 'browse_wishlist',
                'type' => 'text',
                'title' => __('"Browse Wishlist" Text', 'better-wishlist'),
                'default' => 'Browse Wishlist',
            ]);

            $config::add_field('general_settings', [
                'id' => 'wishlist_page_redirect',
                'type' => 'radio',
                'title' => __('Radio', 'rwprs'),
                'title' => __('Redirect to wishlist page', 'better-wishlist'),
                'desc' => __('Select whether redirect after adding to wishlist', 'better-wishlist'),
                'options' => array(
                    true => 'Yes',
                    false => 'No',
                ),
                'default' => false,
            ]);

            $config::add_field('general_settings', [
                'id' => 'cart_page_redirect',
                'type' => 'radio',
                'title' => __('Redirect to cart page', 'better-wishlist'),
                'desc' => __('Select whether redirect cart page after adding to cart from wishlist page', 'better-wishlist'),
                'options' => [
                    true => 'Yes',
                    false => 'No',
                ],
                'default' => false,
            ]);

            $config::add_field('general_settings', [
                'id' => 'remove_from_wishlist',
                'type' => 'radio',
                'title' => __('Remove From Wishlist', 'better-wishlist'),
                'desc' => __('Remove from wishlist after adding to cart', 'better-wishlist'),
                'options' => [
                    true => 'Yes',
                    false => 'No',
                ],
                'default' => false,
            ]);
        });

        new \BetterWishlist\Framework\WPRS('Better Wishlist', 'better-wishlist', 'better_wishlist_settings', 1);
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
            return;
        }

        $product_id = intval($_POST['product_id']);
        $wishlist_id = $this->model->get_current_user_list() ? $this->model->get_current_user_list() : $this->model->create_list();
        $already_in_wishlist = $this->model->item_in_list($product_id, $wishlist_id);

        $data = [
            'product_title' => get_the_title($product_id),
            'message' => __('added in wishlist.', 'better-wishlist'),
        ];

        if ($already_in_wishlist) {
            $data['message'] = __('already exists in wishlist.', 'better-wishlist');
            wp_send_json_error($data);
        }

        // add to wishlist
        $this->model->insert_item($product_id, $wishlist_id);

        wp_send_json_success($data, 200);
    }

    public function remove_from_wishlist()
    {
        check_ajax_referer('better_wishlist_nonce', 'security');

        if (empty($_REQUEST['product_id'])) {
            return;
        }

        $product_id = intval($_POST['product_id']);
        $data = [
            'product_title' => get_the_title($product_id),
            'message' => __('removed from wishlist.', 'better-wishlist'),
        ];

        $removed = Plugin::instance()->model->delete_item($product_id);

        if (!$removed) {
            $data['message'] = __('couldn\'t be removed.', 'better-wishlist');
            wp_send_json_error($data);
        }

        wp_send_json_success($data);
    }

    public function mutiple_product_to_cart()
    {
        check_ajax_referer('better_wishlist_nonce');
        if (empty($_REQUEST['product_ids'])) {
            return false;
        }
        $product_ids = apply_filters('better_wishlist_multiple_product_ids_to_add_to_cart', $_REQUEST['product_ids']);

        $data = array(
            'removed' => false,
            'redirects' => null,
        );

        // if (Better_Wishlist_Helper::get_settings('remove_from_wishlist')) {
        //     $data['removed'] = true;
        // }

        foreach ($product_ids as $id) {

            $product = wc_get_product($id);
            WC()->cart->add_to_cart($id, 1);

            if ($data['removed']) {
                Plugin::instance()->model->delete_item($id);
            }
        }

        // if (Better_Wishlist_Helper::get_settings('cart_page_redirect')) {
        //     $data['redirects'] = wc_get_cart_url();
        // }
        wp_send_json_success($data);
    }

    public function single_product_to_cart()
    {
        check_ajax_referer('better_wishlist_nonce', 'security');

        if (empty($_REQUEST['product_id'])) {
            return;
        }

        $product_id = intval($_REQUEST['product_id']);
        $data = [
            'product_title' => get_the_title($product_id),
            'message' => __('couldn\'t be added.', 'better-wishlist'),
        ];

        if (WC()->cart->add_to_cart($product_id, 1)) {
            $data['message'] = __('added in cart.', 'better-wishlist');

            // if (Better_Wishlist_Helper::get_settings('remove_from_wishlist')) {
            //     Plugin::instance()->model->delete_item($product_id);
            // }

            wp_send_json_success($data);
        }

        wp_send_json_error($data);
    }
}
