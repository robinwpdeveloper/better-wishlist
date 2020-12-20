<?php

namespace BetterWishlist\Framework;

// If this file is called directly, abort.
if (!defined('ABSPATH')) {
    die;
}

class WPRS
{
    protected $slug;
    protected $title;
    protected $option_name;
    protected $api_version;

    use Endpoint;
    use Data;

    /**
     * Setup instance attributes
     *
     * @since     1.0.0
     */
    public function __construct($title, $slug, $option_name, $api_version = 1)
    {
        $this->title = $title;
        $this->slug = $slug;
        $this->option_name = $option_name;
        $this->api_version = $api_version;

        // build settings array
        do_action('wprs_build_settings', Builder::class);

        // add admin menu
        add_action('admin_menu', [$this, 'add_plugin_admin_menu']);

        // add admin scripts
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_styles']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_scripts']);

        // init rest api
        add_action('rest_api_init', [$this, 'register_routes']);

        // save default settings
        add_action('wprs_save_default_settings', [$this, 'save_default_settings']);
    }

    /**
     * Register admin menu.
     *
     * @since    1.0.0
     */
    public function add_plugin_admin_menu()
    {
        add_menu_page(
            $this->title,
            $this->title,
            'manage_options',
            $this->slug,
            [$this, 'display_plugin_admin_page']
        );
    }

    /**
     * Render settings page.
     *
     * @since    1.0.0
     */
    public function display_plugin_admin_page()
    {
        echo '<div id="wprs-admin-root" class="wprs-admin-root"></div>';
    }

    /**
     * Register and enqueue admin-specific style sheet.
     *
     * @since     1.0.0
     */
    public function enqueue_admin_styles($hook_suffix)
    {
        if ('toplevel_page_' . $this->slug !== $hook_suffix) {
            return;
        }

        wp_enqueue_style($this->slug . '-style', BETTER_WISHLIST_PLUGIN_URL . 'public/assets/css/admin.css', array());
    }

    /**
     * Register and enqueue admin-specific javascript
     *
     * @since     1.0.0
     */
    public function enqueue_admin_scripts($hook_suffix)
    {
        if ('toplevel_page_' . $this->slug !== $hook_suffix) {
            return;
        }

        wp_enqueue_script($this->slug . '-admin-script', BETTER_WISHLIST_PLUGIN_URL . 'public/assets/js/admin.js', array('jquery'));
        wp_localize_script($this->slug . '-admin-script', 'wpr_object', [
            'api_nonce' => wp_create_nonce('wp_rest'),
            'api_url' => rest_url($this->slug . '/v1/'),
            'settings' => Builder::get_settings(),
        ]);
    }
}
