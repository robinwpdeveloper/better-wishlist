<?php
/**
 * Better Wishlist Model
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
 * Class Plugin
 */
class Plugin extends Singleton {

	/**
	 * Hold Seed Class Object
	 *
	 * @var Seed
	 * @since 1.0.0
	 */
	public $seed;

	/**
	 * Hold Schedule Class Object
	 *
	 * @var Schedule
	 * @since 1.0.0
	 */
	public $schedule;

	/**
	 * Hold Model Class Object
	 *
	 * @var Model
	 * @since 1.0.0
	 */
	public $model;

	/**
	 * Twig framework template loader
	 *
	 * @var \Twig\Loader\FilesystemLoader
	 * @since 1.0.0
	 */
	public $loader;

	/**
	 * Twig framework local environment
	 *
	 * @var \Twig\Environment
	 * @since 1.0.0
	 */
	public $twig;

	/**
	 * Hold Frontend Class Object
	 *
	 * @var Frontend
	 * @since 1.0.0
	 */
	public $frontend;

	/**
	 * Hold Admin Class Object
	 *
	 * @var $admin
	 * @since 1.0.0
	 */
	public $admin;

	/**
	 * construct
	 * Init this method when object created __construct
	 *
	 * @since 1.0.0
	 */
	protected function __construct() {
		// init modules
		$this->seed     = new Seed();
		$this->schedule = new Schedule();
		$this->model    = new Model();
		$this->loader   = new \Twig\Loader\FilesystemLoader( BETTER_WISHLIST_PLUGIN_PATH . 'public/views' );
		$this->twig     = new \Twig\Environment( $this->loader );
		$this->frontend = new Frontend();

		if ( is_admin() ) {
			new Admin();
		}

		add_filter( 'admin_notices', [ $this, 'add_admin_notice' ], 10, 2 );
		add_filter( 'display_post_states', [ $this, 'add_display_status_on_page' ], 10, 2 );
	}

	/**
	 * add_admin_notice
	 * Show Customized admin notice ni WP dashboard
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function add_admin_notice() {
		if ( class_exists( 'WooCommerce' ) ) {
			return;
		}

		if ( ! function_exists( 'get_plugins' ) ) {
			include_once ABSPATH . '/wp-admin/includes/plugin.php';
		}

		$installed_plugins = get_plugins();
		$basename          = 'woocommerce/woocommerce.php';

		if ( isset( $installed_plugins[ $basename ] ) ) {
			$activation_url = wp_nonce_url( 'plugins.php?action=activate&amp;plugin=' . $basename . '&amp;plugin_status=all&amp;paged=1&amp;s', 'activate-plugin_' . esc_attr( $basename ) );
			/* translators: %1$s: search term %2%s: search term */
			$message = sprintf( __( '%1$sBetterWishlist%2$s requires %1$sWooCommerce%2$s plugin to be active. Please activate WooCommerce to continue.', 'betterwishlist' ), '<strong>', '</strong>' );
			$button  = '<p><a href="' . esc_url( $activation_url ) . '" class="button-primary">' . __( 'Activate WooCommerce', 'betterwishlist' ) . '</a></p>';
		} else {
			$activation_url = wp_nonce_url( self_admin_url( 'update.php?action=install-plugin&plugin=woocommerce' ), 'install-plugin_woocommerce' );
			/* translators: %1$s: search term %2%s: search term */
			$message = sprintf( __( '%1$sBetterWishlist%2$s requires %1$sWooCommerce%2$s plugin to be installed and activated. Please install WooCommerce to continue.', 'betterwishlist' ), '<strong>', '</strong>' );
			$button  = '<p><a href="' . esc_url( $activation_url ) . '" class="button-primary">' . __( 'Install WooCommerce', 'betterwishlist' ) . '</a></p>';
		}

		printf( '<div class="error"><p>%1$s</p>%2$s</div>', $message, $button );// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * add_display_status_on_page
	 * modified page states
	 *
	 * @since 1.0.0
	 * @param mixed $states post states array
	 * @param object $post get single post object
	 * @return mixed
	 */
	public function add_display_status_on_page( $states, $post ) {
		$settings = get_option( 'bw_settings' );

		if ( $post->ID === $settings['wishlist_page'] ) {
			$post_status_object = get_post_status_object( $post->post_status );

			/* Checks if the label exists */
			if ( in_array( $post_status_object->name, $states, true ) ) {
				return $states;
			}

			$states[ $post_status_object->name ] = __( 'Wishlist Page', 'betterwishlist' );
		}

		return $states;
	}
}
