<?php
/**
 * WooCommerce Wishlist.
 * Plugin Name:       Wishlist
 * Plugin URI:        #
 * Description:       Wishlist functionality for your WooCommerce store.
 * Version:           1.0.0
 * Author:            WPDeveloper
 * Author URI:        https://wpdeveloper.net/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wishlist
 * Domain Path:       /languages
 *
 * @package           Wishlist
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Defining plugin constants.
 *
 * @since 3.0.0
 */
define('Wishlist_PLUGIN_FILE', __FILE__);
define('Wishlist_PLUGIN_BASENAME', plugin_basename(__FILE__));
define('Wishlist_PLUGIN_PATH', trailingslashit(plugin_dir_path(__FILE__)));
define('Wishlist_PLUGIN_URL', trailingslashit(plugins_url('/', __FILE__)));
define('Wishlist_PLUGIN_VERSION', '1.0.0');

require(Wishlist_PLUGIN_PATH . 'public/class-addtowishlist.php');

/**
 * The function overwrites the method output templates woocommerce
 *
 * @param string $template_name Name file template.
 * @param array $args Array variable in template.
 * @param string $template_path Customization path.
 */
function wishlist_locate_template($path, $var = null)
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

function wishlist_get_template( $path, $var = null, $return = false ) {
	$located = wishlist_locate_template($path, $var);

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
function view_addto_htmlloop()
{
	$class = Addtowishlist::instance();
	$class->htmloutput_loop();
}


/**
 * Show button Add to Wishlsit
 */
function view_addto_html()
{
	$class = Addtowishlist::instance();
	$class->htmloutput();

	echo '<h2>Hello World</h2>';
}


/**
 * Show button Add to Wishlsit, if product is not purchasable
 */
function view_addto_htmlout()
{
	$class = Addtowishlist::instance();
	$class->htmloutput_out();
}


add_action( 'woocommerce_before_add_to_cart_button', 'view_addto_html', 20 );
add_action( 'catalog_visibility_before_alternate_add_to_cart_button', 'view_addto_html' );
add_action( 'woocommerce_single_product_summary', 'view_addto_htmlout', 29 );
add_action( 'woocommerce_after_shop_loop_item', 'view_addto_htmlout', 29 );
add_action( 'woocommerce_after_shop_loop_item', 'view_addto_htmlloop', 9 );