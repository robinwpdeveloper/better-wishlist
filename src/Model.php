<?php
/**
 * Better Wishlist Model
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
 * Class Model
 */
class Model {

	/**
	 * store session ID
	 *
	 * @since 1.0.0
	 * @access private
	 * @var string $session_id manage session ID
	 */
	private $session_id;

	/**
	 * better_wishlist_lists Table Name
	 *
	 * @since 1.0.0
	 * @access private
	 * @var string $better_wishlist_lists
	 */
	private $better_wishlist_lists;

	/**
	 *  better_wishlist_items Table Name
	 *
	 * @since 1.0.0
	 * @access private
	 * @var string
	 */
	private $better_wishlist_items;

	/**
	 * construct
	 * Init this method when object created __construct
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		global $wpdb;

		// init props
		$this->session_id            = $this->generate_session_id();
		$this->better_wishlist_lists = $wpdb->prefix . 'better_wishlist_lists';
		$this->better_wishlist_items = $wpdb->prefix . 'better_wishlist_items';

		// action
		add_action( 'wp_login', [ $this, 'update_db_and_cookie_on_login' ], 10, 2 );
	}

	/**
	 * generate_session_id
	 * Generate dynamic session id if user are not login
	 *
	 * @return string
	 * @since 1.0.0
	 */
	public function generate_session_id() {
		if ( get_current_user_id() ) {
			return;
		}

		if ( isset( $_COOKIE['better_wishlist_session_id'] ) && ! empty( $_COOKIE['better_wishlist_session_id'] ) ) {
			return $_COOKIE['better_wishlist_session_id'];
		}

		return md5( rand() ); // phpcs:ignore WordPress.WP.AlternativeFunctions.rand_rand
	}

	/**
	 * update_db_and_cookie_on_login
	 * Update db when user login with wishlist items
	 *
	 * @param string $user_login logged-in username
	 * @param mixed $user login user object
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function update_db_and_cookie_on_login( $user_login, $user ) {
		global $wpdb;

		if ( isset( $_COOKIE['better_wishlist_session_id'] ) ) {
			$session_id           = sanitize_text_field( $_COOKIE['better_wishlist_session_id'] );
			$logged_user_wishlist = $wpdb->get_row( "SELECT * FROM {$this->better_wishlist_lists} WHERE user_id={$user->ID}" );
			$wishlist             = $wpdb->get_row( "SELECT ID FROM {$this->better_wishlist_lists} WHERE session_id='{$session_id}'" );

			if ( empty( $logged_user_wishlist ) ) {
				$query = $wpdb->query( "UPDATE {$this->better_wishlist_lists} SET user_id={$user->ID}, expire_on=null, session_id=null WHERE session_id='{$session_id}'" );
				$query = $wpdb->query( "UPDATE {$this->better_wishlist_items} SET user_id={$user->ID} WHERE wishlist_id={$wishlist->ID}" );
			} else {
				if ( $wishlist->ID > 0 ) {
					$query = $wpdb->query( "UPDATE {$this->better_wishlist_items} SET user_id={$user->ID}, wishlist_id={$logged_user_wishlist->ID} WHERE wishlist_id={$wishlist->ID}" );
					$wpdb->query( "DELETE FROM {$this->better_wishlist_lists} WHERE session_id='{$session_id}'" );
				}
			}

			if ( $query ) {
				setcookie( 'better_wishlist_session_id', '', 1, '/' );
			}
		}
	}

	/**
	 * create_list
	 * Added product in wishlist table
	 *
	 * @return mixed
	 * @since 1.0.0
	 */
	public function create_list() {
		global $wpdb;

		$columns = [
			'wishlist_privacy' => '%d',
			'wishlist_name'    => '%s',
			'wishlist_slug'    => '%s',
			'wishlist_token'   => '%s',
			'is_default'       => '%d',
		];

		$values = [
			0,
			__( 'Wishlist', 'betterwishlist' ),
			'',
			uniqid(),
			0,
		];

		if ( ! is_user_logged_in() ) {
			$columns['session_id'] = '%s';
			$values[]              = $this->session_id;

			setcookie( 'better_wishlist_session_id', $this->session_id, time() + 86400, '/' );
		} else {
			$columns['user_id'] = '%d';
			$values[]           = get_current_user_id();
		}

		$columns['created_at'] = 'FROM_UNIXTIME( %d )';
		$values[]              = time();

		if ( ! is_user_logged_in() ) {
			$columns['expire_on'] = 'FROM_UNIXTIME( %d )';
			$timestamp            = strtotime( '+1 day', time() );
			$values[]             = $timestamp;
		}

		$query_columns = implode( ', ', array_map( 'esc_sql', array_keys( $columns ) ) );
		$query_values  = implode( ', ', array_values( $columns ) );
		$query         = "INSERT INTO {$this->better_wishlist_lists} ({$query_columns}) VALUES ({$query_values})";
		$res           = $wpdb->query( $wpdb->prepare( $query, $values ) );

		if ( $res ) {
			return $wpdb->insert_id;
		}

		return false;
	}

	/**
	 * read_list
	 * Fetch product list from wishlist table
	 *
	 * @param mixed $wishlist_id wishlist id
	 *
	 * @return array
	 * @since 1.0.0
	 */
	public function read_list( $wishlist_id ) {
		if ( empty( $wishlist_id ) ) {
			return;
		}

		global $wpdb;

		$wishlist_id = sanitize_text_field( $wishlist_id );
		$query       = "SELECT DISTINCT product_id,user_id,wishlist_id FROM {$this->better_wishlist_items} WHERE wishlist_id={$wishlist_id}";

		return $wpdb->get_results( $query, OBJECT );
	}

	/**
	 * get_current_user_list
	 * Show wishlist data only for login user
	 *
	 * @return false|string
	 * @since 1.0.0
	 */
	public function get_current_user_list() {
		global $wpdb;

		$wishlist_id = null;

		if ( is_user_logged_in() ) {
			$user_id     = get_current_user_id();
			$wishlist_id = $wpdb->get_var( "SELECT ID FROM {$this->better_wishlist_lists} WHERE user_id={$user_id}" );
		} else {
			$wishlist_id = $wpdb->get_var( "SELECT ID FROM {$this->better_wishlist_lists} WHERE session_id='{$this->session_id}'" );
		}

		if ( $wishlist_id ) {
			return $wishlist_id;
		}

		return false;
	}

	/**
	 * item_in_list
	 * Fetch wishlist data from wishlist table
	 *
	 * @sincde 1.0.0
	 *
	 * @param mixed $product_id woocommerce product id
	 * @param mixed $wishlist_id dynamic wishlist id
	 *
	 * @return bool
	 */
	public function item_in_list( $product_id, $wishlist_id ) {
		global $wpdb;

		if ( empty( $product_id ) ) {
			return false;
		}

		if ( empty( $product_id ) ) {
			return false;
		}

		$product_id  = sanitize_text_field( $product_id );
		$wishlist_id = sanitize_text_field( $wishlist_id );
		$result      = $wpdb->get_row( "SELECT * FROM {$this->better_wishlist_items} WHERE wishlist_id='{$wishlist_id}' and product_id={$product_id}" );

		return ! empty( $result );
	}

	/**
	 * insert_item
	 * insert product info in wishlist table
	 *
	 * @param mixed $product_id woocommerce product id
	 * @param mixed $wishlist_id dynamic wishlist id
	 *
	 * @return false|int
	 * @since 1.0.0
	 */
	public function insert_item( $product_id, $wishlist_id ) {
		global $wpdb;

		if ( empty( $product_id ) || empty( $wishlist_id ) ) {
			return false;
		}

		$columns = [
			'product_id'  => '%d',
			'wishlist_id' => '%d',
			'user_id'     => '%d',
		];

		$values = [
			$product_id,
			$wishlist_id,
			get_current_user_id(),
		];

		$columns['created_at'] = 'FROM_UNIXTIME( %d )';
		$values[]              = time();
		$query_columns         = implode( ', ', array_map( 'esc_sql', array_keys( $columns ) ) );
		$query_values          = implode( ', ', array_values( $columns ) );
		$query                 = "INSERT INTO {$this->better_wishlist_items} ( {$query_columns} ) VALUES ( {$query_values} ) ";
		$res                   = $wpdb->query( $wpdb->prepare( $query, $values ) );

		if ( $res ) {
			return $wpdb->insert_id;
		}

		return false;
	}

	/**
	 * delete_item
	 * Delete wishlist item from database after expired or added to cart
	 *
	 * @param mixed $product_id woocommerce product id
	 *
	 * @return bool
	 * @since 1.0.0
	 */
	public function delete_item( $product_id ) {
		if ( empty( $product_id ) ) {
			return false;
		}

		global $wpdb;

		return $wpdb->delete( $this->better_wishlist_items, [ 'product_id' => sanitize_text_field( $product_id ) ], [ '%d' ] );
	}
}
