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
    public $frontend;
    public $admin;

    protected function __construct()
    {
        // init modules
        $this->seed = new Seed;
        $this->schedule = new Schedule;
        $this->model = new Model;
        $this->loader = new \Twig\Loader\FilesystemLoader(BETTER_WISHLIST_PLUGIN_PATH . 'public/views');
        $this->twig = new \Twig\Environment($this->loader);
        $this->frontend = new Frontend;

        if (is_admin()) {
            new Admin;
        }

        add_filter('display_post_states', [$this, 'add_display_status_on_page'], 10, 2);
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
}
