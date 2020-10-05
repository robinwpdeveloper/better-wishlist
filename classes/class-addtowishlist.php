<?php

/**
 * Add to wishlists shortcode and hooks
 *
 * @since             1.0.0
 * @package           Better_Wishlist\Classes
 */

// If this file is called directly, abort.
if (!defined('ABSPATH')) {
    die;
}

/**
 * Add to wishlists shortcode and hooks
 */
class Addtowishlist
{

    /**
     * Single instance of the class
     *
     * @var \Addtowishlist
     * @since 1.0.0
     */
    protected static $instance;

    /**
     * Returns single instance of the class
     *
     * @return \Addtowishlist
     * @since 1.0.0
     */
    public static function get_instance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function htmloutput()
    {
        global $product;

        $current_product = (isset($atts['product_id']) ? wc_get_product($atts['product_id']) : false);
        $current_product = $current_product ? $current_product : $product;

        if (!$current_product) {
            return '';
        }

        $currrent_product_id = $current_product->get_id();
        $current_product_parent = $current_product->get_parent_id();
        $wishlist_url = Helper::get_wishlist_page_id();

        // labels & icons settings.
        $label_text = __('Add to Wishlist', 'wishlist');
        $browse_wishlist_text = __('Browse Wishlist', 'wishlist');

        
        $product_type = $current_product->get_type();
        $already_in_wishlist = __('This product is only in wishlist', 'wishlist');
        $product_added = __('Product Added', 'wishlist');

        $label = apply_filters('wishlist_button_text', $label_text);
        $classes = apply_filters('wishlist_button_classes', ['add_to_wishlist', 'wishlist_button']);

        $is_single = isset($atts['is_single']) ? $atts['is_single'] : Helper::wishlist_is_single();
        $icon = '';
        $exists = false;

        $template_part = $exists && 'add' != 'view' ? 'browse' : 'button';
        $template_part = isset($atts['added_to_wishlist']) ? ($atts['added_to_wishlist'] ? 'added' : 'browse') : $template_part;

        add_action('wishlist_addtowishlist_button', [$this, 'button']);

        $wishlist_id = User_Wishlist()->get_current_user_wishlist();

        $atts = [
            'wishlist_url'  => get_page_link($wishlist_url),
            'in_default_wishlist'   => false,
            'exists'    => $exists,
            'container_classes'   => '',
            'is_single' => $is_single,
            'show_exists' => false,
            'found_in_list' => Wishlist_Item()->is_already_in_wishlist($currrent_product_id, $wishlist_id),
            'product_id'    => $currrent_product_id,
            'parent_product_id' => $current_product_parent ? $current_product_parent : $currrent_product_id,
            'product_type'  => $product_type,
            'label' => $label,
            'show_view' => Helper::wishlist_is_single(),
            'browse_wishlist_text'  => apply_filters('wishlist_browse_wishlist_label', $browse_wishlist_text),
            'already_in_wishlist_text'  => apply_filters('wishlist_already_in_wishlist_text_button', $already_in_wishlist),
            'product_added_text'    => apply_filters('wishlist_product_added_wishlist_message_button', $product_added),
            'icon'  => $icon,
            'heading_icon'  => $icon,
            'link_classes'  => $classes,
            'available_multi_wishlist'  => false,
            'disable_wishlist'  => false,
            'show_count'    => false,
            'ajax_loading'  => true,
            'template_part' => $template_part
        ];

        $atts = apply_filters('wishlist_add_to_wishlist_params', $atts);
        $atts['fragment_options'] = Helper::format_fragment_options($atts, 'wishlist');

        $template = Helper::better_wishlist_get_template('addtowishlist.php', $atts, true);
        echo apply_filters('wishlist_add_to_wishlist_button_html', $template, $wishlist_url, $product_type, $exists);
    }

    public function button($atts)
    {
        $content = apply_filters('wishlist_button_before', '');
        $button_text = apply_filters('wishlist_addtowishlist_text_loop', __('Add to Wishlist', 'wishlist'));
        $text = $button_text;

        if (empty($text)) {
            $icon_class = ' no-txt';
        } else {
            $content .= '<div class="wishlist-clear"></div>';
            $content .= sprintf('<a role="button" aria-label="%s" class="add_to_wishlist_button" data-product-id="%s" data-wishlist-action="add">%s</a>', $button_text, $atts['product_id'], $text);

            $content .= apply_filters('wishlist_button_after', '');
        }

        if (!empty($text)) {
            $content .= '<div class="wishlist-clear"></div>';
        }

        echo apply_filters('wishlist_button', $content);
    }

    public function htmloutput_out()
    {
        global $product;
        if ($product) {
            $this->htmloutput();
        }
    }

    public function htmloutput_loop()
    {
        global $product;

        if ($product) {
            $this->htmloutput();
        }
    }
}
