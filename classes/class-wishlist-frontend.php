<?php

if (!defined('ABSPATH')) {
	exit;
} // Exit if accessed directly


if (!class_exists('Better_Wishlist_Frontend')) {

	class Better_Wishlist_Frontend
	{

		/**
		 * Single instance of the class
		 *
		 * @var \Better_Wishlist_Frontend
		 * @since 1.0.0
		 */
		protected static $instance;

		/**
		 * Returns single instance of the class
		 *
		 * @return \Better_Wishlist_Frontend
		 * @since 1.0.0
		 */
		public static function get_instance()
		{
			if (is_null(self::$instance)) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		public function __construct()
		{
			add_action('wp_enqueue_scripts', [$this, 'register_scripts']);
		}

		public function register_scripts()
		{
			$localize_scripts = $this->get_localize();

			wp_register_script('jquery-wishlist-main', BETTER_WISHLIST_PLUGIN_URL . 'assets/js/' . 'jquery-better-wishlist.js', ['jquery'], '1.0.0', true);
			wp_localize_script('jquery-wishlist-main', 'BETTER_WISHLIST_SCRIPTS', $localize_scripts);

			wp_enqueue_script('jquery-wishlist-main');
		}

		/**
		 * Return localize array
		 *
		 * @return array Array with variables to be localized inside js
		 * @since 2.2.3
		 */
		public function get_localize()
		{
			return apply_filters('better_wishlist_localize_script', [
				'ajax_url' => admin_url('admin-ajax.php', 'relative'),
				'actions' => [
					'add_to_wishlist_action' => 'add_to_wishlist',
					'remove_from_wishlist_action' => 'remove_from_wishlist',
				]
			]);
		}
	}
}

Better_Wishlist_Frontend::get_instance();
