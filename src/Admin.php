<?php
/**
 * Better Wishlist Admin
 *
 * @since 1.0.0
 * @package better-wishlist
 */

namespace BetterWishlist;

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Class Admin
 */
class Admin {

	/**
	 * construct
	 * Init this method when object created __construct
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'admin_menu', [ $this, 'add_plugin_page' ], 99 );
		add_action( 'wp_ajax_bw_save_settings', [ $this, 'save_settings' ] );
	}

	/**
	 * add_plugin_page
	 * Added sub-menu in woocommerce menu section
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function add_plugin_page() {

		$page_hook_suffix = add_submenu_page(
			'woocommerce',
			__( 'Better Wishlist', 'betterwishlist' ),
			__( 'Better Wishlist', 'betterwishlist' ),
			'manage_options',
			'betterwishlist',
			[
				$this,
				'create_admin_page',
			]
		);

		add_action( "admin_print_scripts-{$page_hook_suffix}", [ $this, 'enqueue_admin_scripts' ] );
	}

	/**
	 * create_admin_page
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function create_admin_page() {
		echo '<div id="betterwishlist-admin"></div>';
	}

	/**
	 * enqueue_admin_scripts
	 * Load assets only on admin page
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function enqueue_admin_scripts() {
		wp_enqueue_style( 'betterwishlist-admin-style', BETTER_WISHLIST_PLUGIN_URL . 'public/assets/css/admin.css', [ 'wp-components' ], BETTER_WISHLIST_PLUGIN_VERSION );
		wp_enqueue_script(
			'betterwishlist-admin-script',
			BETTER_WISHLIST_PLUGIN_URL . 'public/assets/js/admin.js',
			[
				'wp-api',
				'wp-api-fetch',
				'wp-i18n',
				'wp-components',
				'wp-element',
			],
			BETTER_WISHLIST_PLUGIN_VERSION,
			true
		);

		wp_localize_script(
			'betterwishlist-admin-script',
			'BetterWishlist',
			[
				'ajaxurl'  => admin_url( 'admin-ajax.php' ),
				'nonce'    => wp_create_nonce( 'betterwishlist' ),
				'settings' => get_option( 'bw_settings' ),
			]
		);
	}

	/**
	 * save_settings
	 * Save all setting data in admin section
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function save_settings() {
		check_ajax_referer( 'betterwishlist', 'security' );

		$settings = array_map( 'sanitize_text_field', $_POST['settings'] );
		$updated  = update_option( 'bw_settings', $settings );

		if ( $updated ) {
			wp_send_json_success();
		}

		wp_send_json_error();
	}

}
