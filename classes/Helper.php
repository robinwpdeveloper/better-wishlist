<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class Helper
{
    public static function wishlist_get_current_url()
    {
        global $wp;

        return apply_filters( 'wishlist_current_url', '', add_query_arg( $wp->query_vars, home_url( $wp->request ) ) );
    }

    /**
     * The function overwrites the method output templates woocommerce
     *
     * @param string $template_name Name file template.
     * @param array $args Array variable in template.
     * @param string $template_path Customization path.
     */
    public static function wishlist_locate_template($path, $var = null)
    {
        $woocommerce_base = WC()->template_path();
        $template_woocommerce_path = $woocommerce_base . $path;
        $template_path = '/' . $path;
        $plugin_path = Wishlist_PLUGIN_PATH . 'templates/' . $path;

        $located = locate_template(
            array(
                $template_woocommerce_path,
                $template_path
            )
        );

        if( ! $located && file_exists($plugin_path) ) {
            return apply_filters( 'wishlist_locate_template', $plugin_path, $path);
        }

        return apply_filters( 'wishlist_locate_template', $located, $path);
    }

    public static function wishlist_get_template( $path, $var = null, $return = false ) {
        $located = self::wishlist_locate_template($path, $var);
    
        if( $var && is_array( $var ) ) {
            $atts = $var;
            extract($var);
        }
    
        if( $return ) {
            ob_start();
        }
    
        include($located);
    
        if( $return ) {
            return ob_get_clean();
        }
    }

    /**
     * Show button Add to Wishlsit, in loop
     */
    public static function view_addto_htmlloop()
    {
        $class = Addtowishlist::instance();
        $class->htmloutput_loop();
    }

    /**
     * Show button Add to Wishlsit, if product is not purchasable
     */
    public static function view_addto_htmlout()
    {
        $class = Addtowishlist::instance();
        $class->htmloutput_out();
        echo '<h1>Hello World</h1>';
    }
}
