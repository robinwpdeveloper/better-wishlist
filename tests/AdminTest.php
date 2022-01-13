<?php
/**
 * Class Admin Test
 *
 * @package Better_Wishlist
 */

use BetterWishlist\Admin;

/**
 * AdminTest test case.
 */
class AdminTest extends WP_UnitTestCase {
	private $admin;

	public function setUp(): void {
		parent::setUp();

		wp_set_current_user( 1 );
		$this->set_admin_role( true );
		$this->admin = new Admin();
	}

	/**
	 * test_init_hook
	 */
	public function test_admin_hook() {
		$admin_menu = has_action( 'admin_menu', [ $this->admin, 'add_plugin_page' ] );
		$this->assertSame( 99, $admin_menu );
	}

	/**
	 * test_ajax hook
	 */
	public function test_ajax_hook() {
		$value = has_action( 'wp_ajax_bw_save_settings', [ $this->admin, 'save_settings' ] );
		$this->assertSame( 10, $value );
	}

	/**
	 * sub_menu hook
	 */
	public function test_sub_menu() {
		global $submenu;
		$this->assertFalse( isset( $submenu['woocommerce'] ) );
		$this->assertFalse( has_action( 'admin_print_scripts-admin_page_betterwishlist', [
			$this->admin,
			'enqueue_admin_scripts'
		] ) );

		$this->admin->add_plugin_page();

		$this->assertTrue( isset( $submenu['woocommerce'] ) );

		$config = $submenu['woocommerce'];

		$this->assertSame( 'BetterWishlist', $config[0][0] );
		$this->assertSame( 'manage_options', $config[0][1] );
		$this->assertSame( 'betterwishlist', $config[0][2] );
		$this->assertSame( 'BetterWishlist', $config[0][3] );

	}

	/**
	 * sub_menu hook
	 */
	public function test_enqueue_script() {
		global $wp_scripts;

		$this->assertFalse( isset( $wp_scripts->registered['betterwishlist-admin-script'] ) );
		$this->admin->enqueue_admin_scripts();
		$this->assertTrue( isset( $wp_scripts->registered['betterwishlist-admin-script'] ) );

		$data = $wp_scripts->get_data( 'betterwishlist-admin-script', 'data' );
		$data = substr( $data, strpos( $data, '{' ) - 1, strpos( $data, '}' ) + 1 );
		$data = json_decode( str_replace( "};", "}", trim( $data ) ), true );

		$this->assertarrayHasKey( 'ajaxurl', $data );
		$this->assertarrayHasKey( 'nonce', $data );
		$this->assertarrayHasKey( 'settings', $data );
		$this->assertSame( 1, wp_verify_nonce( $data['nonce'], 'betterwishlist' ) );
	}

	public function set_admin_role( $enable ) {
		global $current_user;
		if ( $enable ) {
			$current_user->add_role( 'administrator' );
			$current_user->get_role_caps();
		} else {
			$current_user->remove_role( 'administrator' );
			$current_user->get_role_caps();
		}
	}

}
