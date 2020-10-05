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
			$this->includes();

			add_action('init', [$this, 'better_wishlist_install'], 11);
			add_action('woocommerce_after_shop_loop_item', [$this, 'view_addto_htmlloop'], 9);
			add_action('woocommerce_single_product_summary', [$this, 'view_addto_htmlout'], 29);
			add_filter('display_post_states', [$this, 'add_display_status_on_page'], 10, 2);

			add_filter('body_class', [$this, 'add_body_class']);

			add_action('wp_login', [$this, 'update_db_and_cookie_in_login'], 10, 2);
		}

		public function define()
		{
			define('BETTER_WISHLIST_PLUGIN_FILE', __FILE__);
			define('BETTER_WISHLIST_PLUGIN_BASENAME', plugin_basename(__FILE__));
			define('BETTER_WISHLIST_PLUGIN_PATH', trailingslashit(plugin_dir_path(__FILE__)));
			define('BETTER_WISHLIST_PLUGIN_URL', trailingslashit(plugins_url('/', __FILE__)));
			define('BETTER_WISHLIST_PLUGIN_VERSION', '1.0.0');
		}

        public function update_db_and_cookie_in_login($user_login, WP_User $user)
		{
			global $wpdb;

			$session_id = $_COOKIE['wishlist_session_id'];

            if( !empty( $session_id ) ) {
                $query = $wpdb->query("UPDATE {$wpdb->ea_wishlist_lists} SET user_id = {$user->ID}, expiration = null, session_id = null WHERE session_id = '{$session_id}'");

                if($query) {
                    unset($_COOKIE['wishlist_session_id']);
                }
            }
		}

		

		public function better_wishlist_install()
		{
			if (class_exists('WooCommerce')) {
				Better_Wishlist_Install()->init();
			}
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
add_action('init', 'run_better_wishlist');
