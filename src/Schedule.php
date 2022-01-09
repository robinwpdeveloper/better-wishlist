<?php
/**
 * Better Wishlist Schedule
 *
 * @since 1.0.0
 * @package better-wishlist
 */

namespace BetterWishlist;

// If this file is called directly,  abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Class Schedule
 */
class Schedule {

	/**
	 * better_wishlist_lists table name
	 *
	 * @var string $better_wishlist_lists
	 * @since 1.0.0
	 */
	private $better_wishlist_lists;

	/**
	 * better_wishlist_lists table name
	 *
	 * @var string $better_wishlist_items
	 * @since 1.0.0
	 */
	private $better_wishlist_items;

	/**
	 * construct
	 * Init this method, when object created __construct
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'better_wishlist_delete_expired_wishlist', [ $this, 'delete_expired_wishlist' ] );

		if ( ! wp_next_scheduled( 'better_wishlist_delete_expired_wishlist' ) && ! wp_installing() ) {
			wp_schedule_event( time(), 'twicedaily', 'better_wishlist_delete_expired_wishlist' );
		}
	}

	/**
	 * delete_expired_wishlist
	 * Delete all expired wishlist product
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function delete_expired_wishlist() {
		global $wpdb;

		$this->better_wishlist_lists = $wpdb->prefix . 'better_wishlist_lists';
		$this->better_wishlist_items = $wpdb->prefix . 'better_wishlist_items';

		$count = $wpdb->get_var( "SELECT count(ID) FROM {$this->better_wishlist_lists} WHERE CURTIME() >= expire_on AND user_id IS NULL" ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared

		if ( $count > 0 ) {
			$wpdb->query( "DELETE T1,T2 FROM {$this->better_wishlist_lists} T1 INNER JOIN {$this->better_wishlist_items} T2 on T1.ID = T2.wishlist_id WHERE CURTIME() >= expire_on AND T1.user_id IS NULL" ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		}
	}

}
