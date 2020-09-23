<?php
/**
 * Add to wishlists shortcode and hooks
 *
 * @since             1.0.0
 * @package           Wishlist\Public
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Add to wishlists shortcode and hooks
 */
class Addtowishlist {

    /**
	 * This class
	 *
	 * @var \Addtowishlist
	 */
	protected static $_instance = null;

	/**
	 * Get this class object
	 *
	 * @param string $plugin_name Plugin name.
	 *
	 * @return \Addtowishlist
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
    }

    public function __construct() {
        // $this->define_hooks();
    }

    /**
     * Define Hooks
     */
    public function define_hooks()
    {

    }

    public function htmloutput_loop() {
        global $product;

        if($product) {
            $this->htmloutput();
        }
    }

    public function htmloutput($atts = [], $content = null) {
        global $product;

        $current_product = ( isset( $atts['product_id'] ) ? wc_get_product( $atts['product_id'] ) : false );
        $current_product = $current_product ? $current_product : $product;

        if( ! $current_product ) {
            return '';
        }

        $currrent_product_id = $current_product->get_id();
        $current_product_parent = $current_product->get_parent_id();
        $product_type = $current_product->get_type();


        add_action('wishlist_addtowishlist_button', [$this, 'button']);

        wishlist_get_template('addtowishlist.php');
    }

    public function button($echo = true) {
        $content = apply_filters('wishlist_button_before', '');
        $button_text = apply_filters('wishlist_addtowishlist_text_loop', __('Add to Wishlist', 'wishlist') );
        $text = $button_text;
        $action = 'addto';

        if( empty($text) ) {
            $icon_class = ' no-txt';
        }else {
            $content .= '<div class="wishlist-clear"></div>';
            $content .= sprintf('<a role="button" aria-label="%s" class="add_to_wishlist_button" data-tinv-wl-action="add">%s</a>', $button_text, $text);

            $content .= apply_filters('wishlist_button_after', '');
        }

        if ( ! empty( $text ) ) {
			$content .= '<div class="tinv-wishlist-clear"></div>';
        }

        echo apply_filters('wishlist_button', $content);
    }

    public function htmloutput_out() {
        global $product;
        if($product) {
            $this->htmloutput();
        }
    }
    
}