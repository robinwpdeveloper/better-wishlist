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

        // labels & icons settings.
        $label_text = __( 'Add to Wishlist', 'wishlist' );
        $browse_wishlist_text = __( 'Browse Wishlist', 'wishlist' );


        $product_type = $current_product->get_type();
        $already_in_wishlist = __( 'This product is only in wishlist', 'wishlist' );
        $product_added = __( 'Product Added', 'wishlist' );

        $label = apply_filters('wishlist_button_text', $label_text);
        $classes = apply_filters('wishlist_button_classes', ['add_to_wishlist', 'wishlist_button']);

        // TODO: Add mechanism for if product is already in wishlist.

        // TODO: Wishlist URL

        add_action('wishlist_addtowishlist_button', [$this, 'button']);

        $atts = [
            'base_url'  => Helper::wishlist_get_current_url(),
            'product_id'    => $currrent_product_id,
            'parent_product_id' => $current_product_parent,
            'product_type'  => $product_type,
            'container_classes'   => ''
        ];

        // echo '<pre>', print_r($atts, 1), '</pre>';

        echo $currrent_product_id;

        Helper::wishlist_get_template('addtowishlist.php', $atts);
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
            $content .= sprintf('<a role="button" aria-label="%s" class="add_to_wishlist_button" data-wishlist-action="add">%s</a>', $button_text, $text);

            $content .= apply_filters('wishlist_button_after', '');
        }

        if ( ! empty( $text ) ) {
			$content .= '<div class="wishlist-clear"></div>';
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