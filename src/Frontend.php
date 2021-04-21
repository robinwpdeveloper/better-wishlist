<?php

namespace BetterWishlist;

// If this file is called directly,  abort.
if (!defined('ABSPATH')) {
    die;
}

class Frontend
{
    protected $settings;

    public function __construct()
    {
        include_once ABSPATH . 'wp-admin/includes/plugin.php';

        if (!is_plugin_active('woocommerce/woocommerce.php')) {
            return;
        }

        $this->settings = wp_parse_args(get_option('bw_settings'), [
            'position_in_single' => 'after_add_to_cart',
        ]);

        add_action('init', [$this, 'init']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts']);
        add_action('woocommerce_account_betterwishlist_endpoint', array($this, 'menu_content'));
        add_action("woocommerce_{$this->settings['position_in_single']}_button", [$this, 'single_add_to_wishlist_button'], 10);
        add_action('woocommerce_loop_add_to_cart_link', [$this, 'archive_add_to_wishlist_button'], 10, 3);

        // ajax
        add_action('wp_ajax_add_to_wishlist', [$this, 'ajax_add_to_wishlist']);
        add_action('wp_ajax_nopriv_add_to_wishlist', [$this, 'ajax_add_to_wishlist']);

        add_action('wp_ajax_remove_from_wishlist', [$this, 'ajax_remove_from_wishlist']);
        add_action('wp_ajax_nopriv_remove_from_wishlist', [$this, 'ajax_remove_from_wishlist']);

        add_action('wp_ajax_add_to_cart_single', [$this, 'ajax_add_to_cart_single']);
        add_action('wp_ajax_nopriv_add_to_cart_single', [$this, 'ajax_add_to_cart_single']);

        add_action('wp_ajax_add_to_cart_multiple', [$this, 'ajax_add_to_cart_multiple']);
        add_action('wp_ajax_nopriv_add_to_cart_multiple', [$this, 'ajax_add_to_cart_multiple']);

        // filter hooks
        add_filter('body_class', [$this, 'add_body_class']);
        add_filter('woocommerce_account_menu_items', [$this, 'add_menu']);

        // shortcode
        add_shortcode('better_wishlist', [$this, 'shortcode']);
    }

    /**
     * init
     *
     * @return void
     */
    public function init()
    {
        if ($this->settings['wishlist_menu'] == 'yes') {
            add_rewrite_endpoint('betterwishlist', EP_ROOT | EP_PAGES);
        }

        // flush rewrite rules
        if (get_transient('better_wishlist_flush_rewrite_rules') === true) {
            flush_rewrite_rules();
            delete_transient('better_wishlist_flush_rewrite_rules');
        }
    }

    /**
     * enqueue_scripts
     *
     * @return void
     */
    public function enqueue_scripts()
    {
        $settings = get_option('bw_settings');
        $localize_scripts = [
            'ajax_url' => admin_url('admin-ajax.php', 'relative'),
            'nonce' => wp_create_nonce('better_wishlist_nonce'),
            'actions' => [
                'add_to_wishlist' => 'add_to_wishlist',
                'remove_from_wishlist' => 'remove_from_wishlist',
                'add_to_cart_multiple' => 'add_to_cart_multiple',
                'add_to_cart_single' => 'add_to_cart_single',
            ],
            'settings' => [
                'redirect_to_wishlist' => $settings['redirect_to_wishlist'],
                'remove_from_wishlist' => $settings['remove_from_wishlist'],
                'redirect_to_cart' => $settings['redirect_to_cart'],
                'cart_page_url' => wc_get_cart_url(),
                'wishlist_page_url' => esc_url(is_user_logged_in() && $this->settings['wishlist_menu'] == 'yes' ? wc_get_account_endpoint_url('betterwishlist') : get_the_permalink($settings['wishlist_page'])),
            ],
            'i18n' => [
                'no_records_found' => __('No Products Added', 'betterwishlist'),
            ],
        ];

        // css
        wp_register_style('betterwishlist', BETTER_WISHLIST_PLUGIN_URL . 'public/assets/css/' . 'betterwishlist.css', null, BETTER_WISHLIST_PLUGIN_VERSION, 'all');

        // js
        wp_register_script('betterwishlist', BETTER_WISHLIST_PLUGIN_URL . 'public/assets/js/' . 'betterwishlist.js', ['jquery'], BETTER_WISHLIST_PLUGIN_VERSION, true);
        wp_localize_script('betterwishlist', 'BETTER_WISHLIST', $localize_scripts);

        // if woocommerce page, enqueue styles and scripts
        if (is_woocommerce() || is_account_page() || (is_page() && has_shortcode(get_the_content(), 'better_wishlist'))) {
            $css = '';

            if ($this->settings['wishlist_button_style'] == 'custom') {
                $css .= '.betterwishlist-add-to-wishlist {
                    width: ' . $this->settings['wishlist_button_width'] . 'px;
                    color: ' . $this->settings['wishlist_button_color'] . ' !important;
                    background-color: ' . $this->settings['wishlist_button_background'] . ' !important;
                    border-style: ' . $this->settings['wishlist_button_border_style'] . ' !important;
                    border-width: ' . $this->settings['wishlist_button_border_width'] . 'px !important;
                    border-color: ' . $this->settings['wishlist_button_border_color'] . ' !important;
                    padding-top: ' . $this->settings['wishlist_button_padding_top'] . 'px !important;
                    padding-right: ' . $this->settings['wishlist_button_padding_right'] . 'px !important;
                    padding-bottom: ' . $this->settings['wishlist_button_padding_bottom'] . 'px !important;
                    padding-left: ' . $this->settings['wishlist_button_padding_left'] . 'px !important;
                }
                .betterwishlist-add-to-wishlist:hover {
                    color: ' . $this->settings['wishlist_button_hover_color'] . ' !important;
                    background-color: ' . $this->settings['wishlist_button_hover_background'] . ' !important;
                }';
            }

            if ($this->settings['cart_button_style'] == 'custom') {
                $css .= '.betterwishlist-add-to-cart {
                    color: ' . $this->settings['cart_button_color'] . ' !important;
                    background-color: ' . $this->settings['cart_button_background'] . ' !important;
                    border-style: ' . $this->settings['cart_button_border_style'] . ' !important;
                    border-width: ' . $this->settings['cart_button_border_width'] . 'px !important;
                    border-color: ' . $this->settings['cart_button_border_color'] . ' !important;
                    padding-top: ' . $this->settings['cart_button_padding_top'] . 'px !important;
                    padding-right: ' . $this->settings['cart_button_padding_right'] . 'px !important;
                    padding-bottom: ' . $this->settings['cart_button_padding_bottom'] . 'px !important;
                    padding-left: ' . $this->settings['cart_button_padding_left'] . 'px !important;
                }
                .betterwishlist-add-to-cart:hover {
                    color: ' . $this->settings['cart_button_hover_color'] . ' !important;
                    background-color: ' . $this->settings['cart_button_hover_background'] . ' !important;
                }';
            }

            // enqueue styles
            wp_enqueue_style('betterwishlist');
            wp_add_inline_style('betterwishlist', $css);

            // enqueue scripts
            wp_enqueue_script('betterwishlist');
        }
    }

    /**
     * add_body_class
     *
     * @param  mixed $classes
     * @return array
     */
    public function add_body_class($classes)
    {
        if (is_page() && has_shortcode(get_the_content(), 'better_wishlist')) {
            return array_merge($classes, ['woocommerce']);
        }

        return $classes;
    }

    /**
     * add_menu
     *
     * @param  mixed $items
     * @return array
     */
    public function add_menu($items)
    {
        if ($this->settings['wishlist_menu'] != 'yes') {
            return $items;
        }

        $items = array_splice($items, 0, count($items) - 1) + ['betterwishlist' => __('Wishlist', 'betterwishlist')] + $items;

        return $items;
    }

    /**
     * menu_content
     *
     * @return void
     */
    public function menu_content()
    {
        echo do_shortcode('[better_wishlist]');
    }

    /**
     * shortcode
     *
     * @param  array $atts
     * @param  mixed $content
     * @return string
     */
    public function shortcode($atts, $content = null)
    {
        $atts = shortcode_atts([
            'per_page' => 5,
            'current_page' => 1,
            'pagination' => 'no',
            'layout' => '',
        ], $atts);

        $i18n = [
            'product_name' => __('Product name', 'betterwishlist'),
            'stock_status' => __('Stock Status', 'betterwishlist'),
            'add_to_cart' => $this->settings['add_to_cart_text'],
            'add_all_to_cart' => $this->settings['add_all_to_cart_text'],
            'no_records_found' => __('No Product Added', 'betterwishlist'),
            'remove_this_product' => __('Remove this product', 'betterwishlist'),
        ];
        $items = Plugin::instance()->model->read_list(Plugin::instance()->model->get_current_user_list());
        $products = [];

        if ($items) {
            foreach ($items as $item) {
                $product = wc_get_product($item->product_id);

                switch( $product->get_stock_status() ) {
                    case "outofstock":
                        $stock_status = __( "Out Of Stock", "" );
                        break;
                    case "instock":
                        $stock_status = __( "In Stock", "" );
                        break;
                }

                if ($product) {
                    $products[] = [
                        'id' => $product->get_id(),
                        'title' => $product->get_title(),
                        'url' => get_permalink($product->get_id()),
                        'thumbnail_url' => get_the_post_thumbnail_url($product->get_id()),
                        'stock_status' => $stock_status,
                    ];
                }
            }
        }

        return Plugin::instance()->twig->render('page.twig', ['i18n' => $i18n, 'ids' => wp_list_pluck($products, 'id'), 'products' => $products]);
    }

    /**
     * add_to_wishlist_button
     *
     * @return string
     */
    public function add_to_wishlist_button()
    {
        global $product;

        if (!$product) {
            return;
        }

        $i18n = [
            'add_to_wishlist' => $this->settings['add_to_wishlist_text'],
        ];

        return Plugin::instance()->twig->render('button.twig', ['i18n' => $i18n, 'product_id' => $product->get_id()]);
    }

    /**
     * single_add_to_wishlist_button
     *
     * @return void
     */
    public function single_add_to_wishlist_button()
    {
        echo $this->add_to_wishlist_button();
    }

    /**
     * archive_add_to_wishlist_button
     *
     * @param  mixed $add_to_cart_html
     * @param  mixed $product
     * @param  mixed $args
     * @return string
     */
    public function archive_add_to_wishlist_button( $add_to_cart_html )
    {
        if ($this->settings['show_in_loop'] == 'no') {
            return $add_to_cart_html;
        }

        if ($this->settings['position_in_loop'] == 'before_add_to_cart') {
            return $this->add_to_wishlist_button() . $add_to_cart_html;
        }

        return $add_to_cart_html . $this->add_to_wishlist_button();
    }

    /**
     * ajax_add_to_wishlist
     *
     * @return JSON
     */
    public function ajax_add_to_wishlist()
    {
        check_ajax_referer('better_wishlist_nonce', 'security');

        if (empty($_REQUEST['product_id'])) {
            wp_send_json_error([
                'product_title' => '',
                'message' => __('Product ID is should not be empty.', 'betterwishlist'),
            ]);
        }

        $product_id = intval($_POST['product_id']);
        $wishlist_id = Plugin::instance()->model->get_current_user_list() ? Plugin::instance()->model->get_current_user_list() : Plugin::instance()->model->create_list();
        $already_in_wishlist = Plugin::instance()->model->item_in_list($product_id, $wishlist_id);

        if ($already_in_wishlist) {
            wp_send_json_error([
                'product_title' => get_the_title($product_id),
                'message' => __('already exists in wishlist.', 'betterwishlist'),
            ]);
        }

        // add to wishlist
        Plugin::instance()->model->insert_item($product_id, $wishlist_id);

        wp_send_json_success([
            'product_title' => get_the_title($product_id),
            'message' => __('added in wishlist.', 'betterwishlist'),
        ]);
    }

    /**
     * ajax_remove_from_wishlist
     *
     * @return JSON
     */
    public function ajax_remove_from_wishlist()
    {
        check_ajax_referer('better_wishlist_nonce', 'security');

        if (empty($_REQUEST['product_id'])) {
            wp_send_json_error([
                'product_title' => '',
                'message' => __('Product ID is should not be empty.', 'betterwishlist'),
            ]);
        }

        $product_id = intval($_POST['product_id']);
        $removed = Plugin::instance()->model->delete_item($product_id);

        if (!$removed) {
            wp_send_json_error([
                'product_title' => get_the_title($product_id),
                'message' => __('couldn\'t be removed.', 'betterwishlist'),
            ]);
        }

        wp_send_json_success([
            'product_title' => get_the_title($product_id),
            'message' => __('removed from wishlist.', 'betterwishlist'),
        ]);
    }

    /**
     * ajax_add_to_cart_single
     *
     * @return JSON
     */
    public function ajax_add_to_cart_single()
    {
        check_ajax_referer('better_wishlist_nonce', 'security');

        if (empty($_REQUEST['product_id'])) {
            wp_send_json_error([
                'product_title' => '',
                'message' => __('Product ID is should not be empty.', 'betterwishlist'),
            ]);
        }

        $product_id = intval($_REQUEST['product_id']);
        $product = wc_get_product($product_id);

        // add to cart
        if ($product->is_type('variable')) {
            $add_to_cart = WC()->cart->add_to_cart($product_id, 1, $product->get_default_attributes());
        } else {
            $add_to_cart = WC()->cart->add_to_cart($product_id, 1);
        }

        if ($add_to_cart) {
            if ($this->settings['remove_from_wishlist'] == 'yes') {
                Plugin::instance()->model->delete_item($product_id);
            }

            wp_send_json_success([
                'product_title' => get_the_title($product_id),
                'message' => __('added in cart.', 'betterwishlist'),
            ]);
        }

        wp_send_json_error([
            'product_title' => get_the_title($product_id),
            'message' => __('couldn\'t be added in cart.', 'betterwishlist'),
        ]);
    }

    /**
     * ajax_add_to_cart_multiple
     *
     * @return JSON
     */
    public function ajax_add_to_cart_multiple()
    {
        check_ajax_referer('better_wishlist_nonce', 'security');

        if (empty($_REQUEST['products'])) {
            wp_send_json_error([
                'product_title' => '',
                'message' => __('Product ID is should not be empty.', 'betterwishlist'),
            ]);
        }

        foreach ($_REQUEST['products'] as $product_id) {
            WC()->cart->add_to_cart($product_id, 1);

            if ( filter_var( $this->settings['remove_from_wishlist'], FILTER_VALIDATE_BOOLEAN) ) {
                Plugin::instance()->model->delete_item($product_id);
            }
        }

        wp_send_json_success([
            'product_title' => __('All items', 'betterwishlist'),
            'message' => __('added in cart.', 'betterwishlist'),
        ]);
    }
}
