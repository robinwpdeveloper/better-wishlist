<?php
/**
 * Class AjaxTest
 *
 * @package Better_Wishlist
 */

use BetterWishlist\Admin;

/**
 * AdminTest test case.
 */
class AjaxTest extends WP_Ajax_UnitTestCase {

	private $admin;

	public function setUp(): void {
		parent::setUp();
		$this->admin = new Admin();
	}

	/**
	 * test_save_setting
	 */
	public function test_save_setting() {
		$_POST[ 'security' ] = wp_create_nonce( 'betterwishlist' );
		$_POST[ 'settings' ] = [
			'data _1' => 'data 1',
			'data _2' => 'data 2'
		];

		try {
			$this->_handleAjax( 'bw_save_settings' );
		} catch ( Exception $e ) {

		}
		$response = json_decode( $this->_last_response );
		$this->assertIsObject( $response );
		$this->assertObjectHasAttribute( 'success', $response );
		$this->assertSame(true, $response->success);
	}

	/**
	 * test_save_setting
	 */
	public function test_save_setting_wrong_nonce() {
		$_POST[ 'security' ] = wp_create_nonce( 'betterwishlist_test' );
		$_POST[ 'settings' ] = [
			'data _1' => 'data 1',
			'data _2' => 'data 2'
		];

		try {
			$this->_handleAjax( 'bw_save_settings' );
		} catch ( Exception $e ) {

		}
		$response = json_decode( $this->_last_response );
		$this->assertNull( $response );
	}

}
