<?php

/**
 * Plugin Name:       Better Wishlist
 * Plugin URI:        #
 * Description:       Wishlist functionality for your WooCommerce store.
 * Version:           1.0.0
 * Author:            WPDeveloper
 * Author URI:        https://wpdeveloper.net/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       better-wishlist
 * Domain Path:       /languages
 *
 * @package           Better_Wishlist
 */

// If this file is called directly, abort.
if (!defined('ABSPATH')) {
	die;
}


if (!class_exists('Better_Wishlist')) {
	class Better_Wishlist
	{

		public function __construct()
		{

			$this->define();
            $this->define_table();
			$this->includes();

			add_action('woocommerce_after_shop_loop_item', [$this, 'view_addto_htmlloop'], 9);
			add_action('woocommerce_single_product_summary', [$this, 'view_addto_htmlout'], 29);
			add_filter('display_post_states', [$this, 'add_display_status_on_page'], 10, 2);

			add_filter('body_class', [$this, 'add_body_class']);

			add_action('wp_login', ['Better_Wishlist_HelperBetter_Wishlist_Helper', 'update_db_and_cookie_in_login'], 10, 2);
			add_action('better_wishlist_delete_expired_wishlist_cron_hook', ['Better_Wishlist_Helper','delete_expired_wishlist']);
			$this->scheduled_remove_wishlist();

			$this->active_plugin();
		}

		public function define_table(){
            global $wpdb;

            $wpdb->ea_wishlist_items = $wpdb->prefix . 'wishlist_item';
            $wpdb->ea_wishlist_lists = $wpdb->prefix . 'wishlist_item_lists';
        }

		/**
		 * Check if there is a hook in the cron
		 */
		function scheduled_remove_wishlist()
		{
			if ( ! wp_next_scheduled( 'better_wishlist_delete_expired_wishlist_cron_hook' ) && ! wp_installing()  ) {
				wp_schedule_event( time(), 'twicedaily', 'better_wishlist_delete_expired_wishlist_cron_hook' );
			}
		}


		public function define()
		{
			define('BETTER_WISHLIST_PLUGIN_FILE', __FILE__);
			define('BETTER_WISHLIST_PLUGIN_BASENAME', plugin_basename(__FILE__));
			define('BETTER_WISHLIST_PLUGIN_PATH', trailingslashit(plugin_dir_path(__FILE__)));
			define('BETTER_WISHLIST_PLUGIN_URL', trailingslashit(plugins_url('/', __FILE__)));
			define('BETTER_WISHLIST_PLUGIN_VERSION', '1.0.0');
		}

		public function add_display_status_on_page($states, $post)
		{
			if (get_option('better_wishlist_page_id') == $post->ID) {
				$post_status_object = get_post_status_object($post->post_status);

				/* Checks if the label exists */
				if (in_array($post_status_object->name, $states, true)) {
					return $states;
				}

				$states[$post_status_object->name] = __('Wishlist Page', 'wishlist');
			}

			return $states;
		}

		public function add_body_class($classes)
		{
			if (is_page() && get_the_ID() == get_option('better_wishlist_page_id')) {
				return array_merge($classes, ['woocommerce']);
			}
			return $classes;
		}

		/**
		 * Including all necessary files.
		 * 
		 * @since 1.0.0
		 */
		public function includes()
		{
			require_once BETTER_WISHLIST_PLUGIN_PATH . 'classes/class-wishlist-install.php';
			require_once BETTER_WISHLIST_PLUGIN_PATH . 'classes/class-addtowishlist.php';
			require_once BETTER_WISHLIST_PLUGIN_PATH . 'classes/Helper.php';
			require_once BETTER_WISHLIST_PLUGIN_PATH . 'classes/class-wishlist-frontend.php';
			require_once BETTER_WISHLIST_PLUGIN_PATH . 'classes/class-wishlist-form-handler.php';
			require_once BETTER_WISHLIST_PLUGIN_PATH . 'classes/class-user-wishlist.php';
			require_once BETTER_WISHLIST_PLUGIN_PATH . 'classes/class-wishlist-item.php';
			require_once BETTER_WISHLIST_PLUGIN_PATH . 'classes/class-wishlist-shortcode.php';
		}

		/**
		 * Show button Add to Wishlsit, in loop
		 */
		public static function view_addto_htmlloop()
		{
			$class = Addtowishlist::get_instance();
			$class->htmloutput_loop();
		}

		/**
		 * Show button Add to Wishlsit, if product is not purchasable
		 */
		public static function view_addto_htmlout()
		{
			$class = Addtowishlist::get_instance();
			$class->htmloutput_out();
		}

		public function active_plugin(){
            register_activation_hook( __FILE__, array( 'Better_Wishlist_Install','install' ) );
        }

	}


}

/**
 * Running the plugin.
 * 
 * @since 1.0.0
 */
function run_better_wishlist()
{
	$wishlist = new Better_Wishlist();
}

run_better_wishlist();


