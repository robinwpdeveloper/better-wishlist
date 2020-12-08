<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class Better_Wishlist_Helper {
    /**
     * The function overwrites the method output templates woocommerce
     *
     * @param  string  $template_name  Name file template.
     * @param  array   $args           Array variable in template.
     * @param  string  $template_path  Customization path.
     */
    // public static function better_wishlist_locate_template ($path, $var = null) {
    //     $woocommerce_base = WC()->template_path();
    //     $template_woocommerce_path = $woocommerce_base.$path;
    //     $template_path = '/'.$path;
    //     $plugin_path = BETTER_WISHLIST_PLUGIN_PATH.'templates/'.$path;

    //     $located = locate_template(
    //         array(
    //             $template_woocommerce_path,
    //             $template_path
    //         )
    //     );

    //     if (!$located && file_exists($plugin_path)) {
    //         return apply_filters('better_wishlist_locate_template', $plugin_path, $path);
    //     }

    //     return apply_filters('better_wishlist_locate_template', $located, $path);
    // }

    // public static function better_wishlist_get_template ($path, $var = null, $return = false) {
    //     $located = self::better_wishlist_locate_template($path, $var);

    //     if ($var && is_array($var)) {
    //         $atts = $var;
    //         extract($var);
    //     }

    //     if ($return) {
    //         ob_start();
    //     }

    //     include($located);

    //     if ($return) {
    //         return ob_get_clean();
    //     }
    // }

    /**
     * Retrive wishlist page id, if any
     *
     * @return int Wishlist page id
     */
    public static function get_wishlist_page_id () {
        $wishlist_page_id = get_option('better_wishlist_page_id');

        return apply_filters('better_wishlist_page_id', $wishlist_page_id);
    }

    /**
     * Retrive wishlist settings from database
     *
     * @return string Wishlist settings value
     */

    public static function get_settings($key)
    {
        global $better_wishlist_settings;
        if (isset($better_wishlist_settings->{$key})) {
            return $better_wishlist_settings->{$key};
        }
        return;
    }


    /**
     * Returns true if it finds that you're printing a single product
     * Should return false in any loop (including the ones inside single product page)
     *
     * @return bool Whether you're currently on single product template
     * @since 3.0.0
     */
    public static function wishlist_is_single () {
        return apply_filters('better_wishlist_is_single', is_product() && !in_array(wc_get_loop_prop('name'),
                array('related', 'up-sells')) && !wc_get_loop_prop('is_shortcode'));
    }
}
