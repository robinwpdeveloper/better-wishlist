<?php
/**
 * Better Wishlist Singleton
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
 * Class Singleton
 */
class Singleton {

	/**
	 * Hold object
	 *
	 * @var null $instance
	 */
	private static $instance = null;


	/**
	 * instance
	 * Create instance only once when plugin bootstrap
	 *
	 * @since 1.0.0
	 * @return static|null
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new static();
		}

		return self::$instance;
	}
}
