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

        add_filter('admin_notices', [$this, 'add_admin_notice'], 10, 2);
        add_filter('display_post_states', [$this, 'add_display_status_on_page'], 10, 2);
    }

    public function add_admin_notice()
    {
        if (class_exists('WooCommerce')) {
            return;
        }

        if (!function_exists('get_plugins')) {
            include_once ABSPATH . '/wp-admin/includes/plugin.php';
        }

        $installed_plugins = get_plugins();
        $basename = 'woocommerce/woocommerce.php';

        if (isset($installed_plugins[$basename])) {
            $activation_url = wp_nonce_url('plugins.php?action=activate&amp;plugin=' . $basename . '&amp;plugin_status=all&amp;paged=1&amp;s', 'activate-plugin_' . $basename);
            $message = sprintf(__('%1$sBetter Wishlist%2$s requires %1$sWooCommerce%2$s plugin to be active. Please activate WooCommerce to continue.', 'betterwishlist'), "<strong>", "</strong>");
            $button = '<p><a href="' . $activation_url . '" class="button-primary">' . __('Activate WooCommerce', 'betterwishlist') . '</a></p>';
        } else {
            $activation_url = wp_nonce_url(self_admin_url('update.php?action=install-plugin&plugin=woocommerce'), 'install-plugin_woocommerce');
            $message = sprintf(__('%1$sBetter Wishlist%2$s requires %1$sWooCommerce%2$s plugin to be installed and activated. Please install WooCommerce to continue.', 'betterwishlist'), '<strong>', '</strong>');
            $button = '<p><a href="' . $activation_url . '" class="button-primary">' . __('Install WooCommerce', 'betterwishlist') . '</a></p>';
        }

        printf('<div class="error"><p>%1$s</p>%2$s</div>', $message, $button);
    }

    public function add_display_status_on_page($states, $post)
    {
        if (get_option('better_wishlist_page_id') == $post->ID) {
            $post_status_object = get_post_status_object($post->post_status);

            /* Checks if the label exists */
            if (in_array($post_status_object->name, $states, true)) {
                return $states;
            }

            $states[$post_status_object->name] = __('Wishlist Page', 'betterwishlist');
        }

        return $states;
    }
}
