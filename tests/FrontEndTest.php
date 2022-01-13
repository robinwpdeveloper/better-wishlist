<?php
/**
* Class Admin Test
*
* @package Better_Wishlist
*/

use BetterWishlist\Frontend;

/**
* AdminTest test case.
*/
class FrontEndTest extends WP_UnitTestCase {
	private $front_end;

	public function setUp(): void {
		parent::setUp();

		$this->front_end = new Frontend();
	}

	/**
	 * test_Fontend hooks
	 */
	public function test_admin_hook() {
		$this->assertSame(10, has_action( 'init', [ $this->front_end, 'init' ] ) );
		$this->assertSame(10, has_action( 'wp_enqueue_scripts', [ $this->front_end, 'enqueue_scripts' ] ) );
	}

}
