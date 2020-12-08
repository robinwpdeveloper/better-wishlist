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
    public $model;
    public $loader;
    public $twig;

    protected function __construct()
    {
        // init modules
        $this->seed = new Seed;
        $this->schedule = new Schedule;
        $this->model = new Model;
        $this->loader = new \Twig\Loader\FilesystemLoader(BETTER_WISHLIST_PLUGIN_PATH . 'public/views');
        $this->twig = new \Twig\Environment($this->loader);

        // create instance of coworkers
        new Template();

        add_action('woocommerce_after_shop_loop_item', [$this, 'view_addto_htmlloop'], 9);
        add_action('woocommerce_single_product_summary', [$this, 'view_addto_htmlout'], 29);
        add_filter('display_post_states', [$this, 'add_display_status_on_page'], 10, 2);

        add_filter('body_class', [$this, 'add_body_class']);

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

    /**
     * Show button Add to Wishlsit, in loop
     */
    public static function view_addto_htmlloop()
    {
        $class = Addtowishlist::get_instance();
        $class->htmloutput_loop();
    }

    /**
     * Show button Add to Wishlsit, if product is not purchasable
     */
    public static function view_addto_htmlout()
    {
        $class = Addtowishlist::get_instance();
        $class->htmloutput_out();
    }
}
