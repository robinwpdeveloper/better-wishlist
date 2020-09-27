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

define('Wishlist_PLUGIN_FILE', __FILE__);
define('Wishlist_PLUGIN_BASENAME', plugin_basename(__FILE__));
define('Wishlist_PLUGIN_PATH', trailingslashit(plugin_dir_path(__FILE__)));
define('Wishlist_PLUGIN_URL', trailingslashit(plugins_url('/', __FILE__)));
define('Wishlist_PLUGIN_VERSION', '1.0.0');

require_once Wishlist_PLUGIN_PATH . 'classes/class-wishlist-install.php';
register_activation_hook(__FILE__, function () {
	if( class_exists( 'WooCommerce') ) {
		Wishlist_Install()->init();
	}
});


if( ! class_exists( 'Wishlist' ) ) {
	class Wishlist {

		public function __construct()
		{
			add_action( 'woocommerce_after_shop_loop_item', ['Helper', 'view_addto_htmlloop'], 9 );
			add_action( 'woocommerce_single_product_summary', ['Helper', 'view_addto_htmlout'], 29 );
			add_filter('display_post_states', [$this, 'add_display_status_on_page'], 10, 2);
		}
	
		public function init()
		{
			$this->includes();
		}
	
        public function add_display_status_on_page($states, $post)
        {
            if(get_option('ea_wishlist_page_id') == $post->ID) {
                $post_status_object = get_post_status_object( $post->post_status );

                /* Checks if the label exists */
                if ( in_array( $post_status_object->name, $states, true ) ) {
                    return $states;
                }
                
                $states[ $post_status_object->name ] = __( 'Wishlist Page', 'wishlist' );
            }

            return $states;
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




