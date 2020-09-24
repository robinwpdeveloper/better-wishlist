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

if( ! class_exists( 'Wishlist' ) ) {
	class Wishlist {

		public function __construct()
		{
			add_action( 'woocommerce_after_shop_loop_item', ['Helper', 'view_addto_htmlloop'], 9 );
			add_action( 'woocommerce_single_product_summary', ['Helper', 'view_addto_htmlout'], 29 );
		}
	
		public function init()
		{
			$this->define_hooks();
			$this->includes();
			$this->install();
		}

		public function install()
		{
			if(!Wishlist_Install()->is_installed()) {
				Wishlist_Install()->init();
			}
		}
	
		/**
		 * Defining plugin constants.
		 *
		 * @since 1.0.0
		 */
		public function define_hooks()
		{
			define('Wishlist_PLUGIN_FILE', __FILE__);
			define('Wishlist_PLUGIN_BASENAME', plugin_basename(__FILE__));
			define('Wishlist_PLUGIN_PATH', trailingslashit(plugin_dir_path(__FILE__)));
			define('Wishlist_PLUGIN_URL', trailingslashit(plugins_url('/', __FILE__)));
			define('Wishlist_PLUGIN_VERSION', '1.0.0');
		}
		
		/**
		 * Including all necessary files.
		 * 
		 * @since 1.0.0
		 */
		public function includes()
		{
			require_once Wishlist_PLUGIN_PATH . 'public/class-addtowishlist.php';
			require_once Wishlist_PLUGIN_PATH . 'classes/Helper.php';
			require_once Wishlist_PLUGIN_PATH . 'classes/class-wishlist-install.php';
		}
	}


	/**
	 * Running the plugin.
	 * 
	 * @since 1.0.0
	 */
	function run_wishlist() {
		$wishlist = new Wishlist();
		$wishlist->init();
	}
	add_action('plugins_loaded', 'run_wishlist');	
}