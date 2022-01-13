<?php
/**
 * Better Wishlist Frontend
 *
 * @since 1.0.0
 * @package better-wishlist
 */

namespace BetterWishlist;

// If this file is called directly,  abort.
use Exception;

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Class Frontend
 */
class Frontend {

	/**
	 * Store settings data from admin settings
	 *
	 * @since 1.0.0
	 * @var array|object $settings
	 */
	protected $settings;

	/**
	 * construct
	 * Init this method when object created __construct
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		include_once ABSPATH . 'wp-admin/includes/plugin.php';

		if ( ! is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
			return;
		}

		$this->settings = wp_parse_args(
			get_option( 'bw_settings' ),
			[
				'position_in_single' => 'after_add_to_cart',
			]
		);

		add_action( 'init', [ $this, 'init' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
		add_action( 'woocommerce_account_betterwishlist_endpoint', array( $this, 'menu_content' ) );
		add_action( "woocommerce_{$this->settings['position_in_single']}_button", [ $this, 'single_add_to_wishlist_button' ], 10 );
		add_action( 'woocommerce_loop_add_to_cart_link', [ $this, 'archive_add_to_wishlist_button' ], 10, 3 );

		// ajax
		add_action( 'wp_ajax_add_to_wishlist', [ $this, 'ajax_add_to_wishlist' ] );
		add_action( 'wp_ajax_nopriv_add_to_wishlist', [ $this, 'ajax_add_to_wishlist' ] );

		add_action( 'wp_ajax_remove_from_wishlist', [ $this, 'ajax_remove_from_wishlist' ] );
		add_action( 'wp_ajax_nopriv_remove_from_wishlist', [ $this, 'ajax_remove_from_wishlist' ] );

		add_action( 'wp_ajax_add_to_cart_single', [ $this, 'ajax_add_to_cart_single' ] );
		add_action( 'wp_ajax_nopriv_add_to_cart_single', [ $this, 'ajax_add_to_cart_single' ] );

		add_action( 'wp_ajax_add_to_cart_multiple', [ $this, 'ajax_add_to_cart_multiple' ] );
		add_action( 'wp_ajax_nopriv_add_to_cart_multiple', [ $this, 'ajax_add_to_cart_multiple' ] );

		// filter hooks
		add_filter( 'body_class', [ $this, 'add_body_class' ] );
		add_filter( 'woocommerce_account_menu_items', [ $this, 'add_menu' ] );

		// shortcode
		add_shortcode( 'better_wishlist', [ $this, 'shortcode' ] );
	}

	/**
	 * init
	 * Initialize wp rewrite rule
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function init() {
		if ( 'yes' === $this->settings['wishlist_menu'] ) {
			add_rewrite_endpoint( 'betterwishlist', EP_ROOT | EP_PAGES );
		}

		// flush rewrite rules
		if ( get_transient( 'better_wishlist_flush_rewrite_rules' ) === true ) {
			flush_rewrite_rules();
			delete_transient( 'better_wishlist_flush_rewrite_rules' );
		}
	}

	/**
	 * enqueue_scripts
	 * Enqueue All script /css which are responsible for frontend
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function enqueue_scripts() {
		$settings         = get_option( 'bw_settings' );
		$localize_scripts = [
			'ajax_url' => admin_url( 'admin-ajax.php', 'relative' ),
			'nonce'    => wp_create_nonce( 'better_wishlist_nonce' ),
			'actions'  => [
				'add_to_wishlist'      => 'add_to_wishlist',
				'remove_from_wishlist' => 'remove_from_wishlist',
				'add_to_cart_multiple' => 'add_to_cart_multiple',
				'add_to_cart_single'   => 'add_to_cart_single',
			],
			'settings' => [
				'redirect_to_wishlist' => $settings['redirect_to_wishlist'],
				'remove_from_wishlist' => $settings['remove_from_wishlist'],
				'redirect_to_cart'     => $settings['redirect_to_cart'],
				'cart_page_url'        => wc_get_cart_url(),
				'wishlist_page_url'    => esc_url( is_user_logged_in() && 'yes' === $this->settings['wishlist_menu'] ? wc_get_account_endpoint_url( 'betterwishlist' ) : get_the_permalink( $settings['wishlist_page'] ) ),
			],
			'i18n'     => [
				'no_records_found' => __( 'No Products Added', 'betterwishlist' ),
			],
		];

		// css
		wp_register_style( 'betterwishlist', BETTER_WISHLIST_PLUGIN_URL . 'public/assets/css/betterwishlist.css', null, BETTER_WISHLIST_PLUGIN_VERSION, 'all' );

		// js
		wp_register_script( 'betterwishlist', BETTER_WISHLIST_PLUGIN_URL . 'public/assets/js/betterwishlist.js', [ 'jquery' ], BETTER_WISHLIST_PLUGIN_VERSION, true );
		wp_localize_script( 'betterwishlist', 'BETTER_WISHLIST', $localize_scripts );

		// if woocommerce page, enqueue styles and scripts
		if ( is_woocommerce() || is_account_page() || ( is_page() && has_shortcode( get_the_content(), 'better_wishlist' ) ) ) {
			$css = '';

			if ( 'custom' === $this->settings['wishlist_button_style'] ) {
				$css .= '.betterwishlist-add-to-wishlist {
                    width: ' . $this->settings['wishlist_button_width'] . 'px;
                    color: ' . $this->settings['wishlist_button_color'] . ' !important;
                    background-color: ' . $this->settings['wishlist_button_background'] . ' !important;
                    border-style: ' . $this->settings['wishlist_button_border_style'] . ' !important;
                    border-width: ' . $this->settings['wishlist_button_border_width'] . 'px !important;
                    border-color: ' . $this->settings['wishlist_button_border_color'] . ' !important;
                    padding-top: ' . $this->settings['wishlist_button_padding_top'] . 'px !important;
                    padding-right: ' . $this->settings['wishlist_button_padding_right'] . 'px !important;
                    padding-bottom: ' . $this->settings['wishlist_button_padding_bottom'] . 'px !important;
                    padding-left: ' . $this->settings['wishlist_button_padding_left'] . 'px !important;
                }
                .betterwishlist-add-to-wishlist:hover {
                    color: ' . $this->settings['wishlist_button_hover_color'] . ' !important;
                    background-color: ' . $this->settings['wishlist_button_hover_background'] . ' !important;
                }';
			}

			if ( 'custom' === $this->settings['cart_button_style'] ) {
				$css .= '.betterwishlist-add-to-cart {
                    color: ' . $this->settings['cart_button_color'] . ' !important;
                    background-color: ' . $this->settings['cart_button_background'] . ' !important;
                    border-style: ' . $this->settings['cart_button_border_style'] . ' !important;
                    border-width: ' . $this->settings['cart_button_border_width'] . 'px !important;
                    border-color: ' . $this->settings['cart_button_border_color'] . ' !important;
                    padding-top: ' . $this->settings['cart_button_padding_top'] . 'px !important;
                    padding-right: ' . $this->settings['cart_button_padding_right'] . 'px !important;
                    padding-bottom: ' . $this->settings['cart_button_padding_bottom'] . 'px !important;
                    padding-left: ' . $this->settings['cart_button_padding_left'] . 'px !important;
                }
                .betterwishlist-add-to-cart:hover {
                    color: ' . $this->settings['cart_button_hover_color'] . ' !important;
                    background-color: ' . $this->settings['cart_button_hover_background'] . ' !important;
                }';
			}

			// enqueue styles
			wp_enqueue_style( 'betterwishlist' );
			wp_add_inline_style( 'betterwishlist', $css );

			// enqueue scripts
			wp_enqueue_script( 'betterwishlist' );
		}//end if
	}

	/**
	 * add_body_class
	 *
	 * @since 1.0.0
	 * @param mixed $classes hold all css classes
	 * @return array
	 */
	public function add_body_class( $classes ) {
		if ( is_page() && has_shortcode( get_the_content(), 'better_wishlist' ) ) {
			return array_merge( $classes, [ 'woocommerce' ] );
		}

		return $classes;
	}

	/**
	 * add_menu
	 * added menu as a woocommerce sub menu
	 *
	 * @since 1.0.0
	 * @param mixed $items all woocommerce menu list
	 * @return array
	 */
	public function add_menu( $items ) {
		if ( 'yes' !== $this->settings['wishlist_menu'] ) {
			return $items;
		}

		return array_splice( $items, 0, count( $items ) - 1 ) + [ 'betterwishlist' => __( 'Wishlist', 'betterwishlist' ) ] + $items;
	}

	/**
	 * menu_content
	 * print shortcode
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function menu_content() {
		echo do_shortcode( '[better_wishlist]' );
	}

	/**
	 * shortcode
	 * Build shortcode with necessary data
	 *
	 * @since 1.0.0
	 * @param array $atts short code attribute
	 * @return string
	 */
	public function shortcode( $atts ) {
		$atts = shortcode_atts(
			[
				'per_page'     => 5,
				'current_page' => 1,
				'pagination'   => 'no',
				'layout'       => '',
			],
			$atts
		);

		$i18n     = [
			'product_name'        => __( 'Product name', 'betterwishlist' ),
			'stock_status'        => __( 'Stock Status', 'betterwishlist' ),
			'add_to_cart'         => $this->settings['add_to_cart_text'],
			'add_all_to_cart'     => $this->settings['add_all_to_cart_text'],
			'no_records_found'    => __( 'No Product Added', 'betterwishlist' ),
			'remove_this_product' => __( 'Remove this product', 'betterwishlist' ),
		];
		$items    = Plugin::instance()->model->read_list( Plugin::instance()->model->get_current_user_list() );
		$products = [];

		if ( $items ) {
			foreach ( $items as $item ) {
				$product = wc_get_product( $item->product_id );

				switch ( $product->get_stock_status() ) {
					case 'outofstock':
						$stock_status = __( 'Out Of Stock', 'betterwishlist' );
						break;
					case 'instock':
						$stock_status = __( 'In Stock', 'betterwishlist' );
						break;
				}

				if ( $product ) {
					$products[] = [
						'id'            => $product->get_id(),
						'title'         => $product->get_title(),
						'url'           => get_permalink( $product->get_id() ),
						'thumbnail_url' => get_the_post_thumbnail_url( $product->get_id() ),
						'stock_status'  => $stock_status,
					];
				}
			}//end foreach
		}//end if

		return Plugin::instance()->twig->render(
			'page.twig',
			[
				'i18n'     => $i18n,
				'ids'      => wp_list_pluck( $products, 'id' ),
				'products' => $products,
			]
		);
	}

	/**
	 * add_to_wishlist_button
	 * Added wishlist button in each woocommerce product
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function add_to_wishlist_button() {
		global $product;

		if ( ! $product ) {
			return null;
		}

		$i18n = [
			'add_to_wishlist' => $this->settings['add_to_wishlist_text'],
		];

		return Plugin::instance()->twig->render(
			'button.twig',
			[
				'i18n'       => $i18n,
				'product_id' => $product->get_id(),
			]
		);
	}

	/**
	 * single_add_to_wishlist_button
	 * Added wishlist button in single product page
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function single_add_to_wishlist_button() {
		// @codingStandardsIgnoreStart
		echo $this->add_to_wishlist_button();
		// @codingStandardsIgnoreEnd
	}

	/**
	 * archive_add_to_wishlist_button
	 * Show wishlist page link based on admin setting
	 *
	 * @since 1.0.0
	 * @param mixed $add_to_cart_html add to cart button markup
	 * @return string
	 */
	public function archive_add_to_wishlist_button( $add_to_cart_html ) {
		if ( 'no' === $this->settings['show_in_loop'] ) {
			return $add_to_cart_html;
		}

		if ( 'before_add_to_cart' === $this->settings['position_in_loop'] ) {
			return $this->add_to_wishlist_button() . $add_to_cart_html;
		}

		return $add_to_cart_html . $this->add_to_wishlist_button();
	}

	/**
	 * ajax_add_to_wishlist
	 * Product added in wishlist when click add to wishlist button
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function ajax_add_to_wishlist() {
		check_ajax_referer( 'better_wishlist_nonce', 'security' );

		if ( empty( $_REQUEST['product_id'] ) ) {
			wp_send_json_error(
				[
					'product_title' => '',
					'message'       => __( 'Product ID is should not be empty.', 'betterwishlist' ),
				]
			);
		}

		$product_id          = intval( $_POST['product_id'] );
		$wishlist_id         = Plugin::instance()->model->get_current_user_list() ? Plugin::instance()->model->get_current_user_list() : Plugin::instance()->model->create_list();
		$already_in_wishlist = Plugin::instance()->model->item_in_list( $product_id, $wishlist_id );

		if ( $already_in_wishlist ) {
			wp_send_json_error(
				[
					'product_title' => get_the_title( $product_id ),
					'message'       => __( 'already exists in wishlist.', 'betterwishlist' ),
				]
			);
		}

		// add to wishlist
		Plugin::instance()->model->insert_item( $product_id, $wishlist_id );

		wp_send_json_success(
			[
				'product_title' => get_the_title( $product_id ),
				'message'       => __( 'added in wishlist.', 'betterwishlist' ),
			]
		);
	}

	/**
	 * ajax_remove_from_wishlist
	 * Remove Product from wishlist after click remove Button/Icon
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function ajax_remove_from_wishlist() {
		check_ajax_referer( 'better_wishlist_nonce', 'security' );

		if ( empty( $_REQUEST['product_id'] ) ) {
			wp_send_json_error(
				[
					'product_title' => '',
					'message'       => __( 'Product ID is should not be empty.', 'betterwishlist' ),
				]
			);
		}

		$product_id = intval( $_POST['product_id'] );
		$removed    = Plugin::instance()->model->delete_item( $product_id );

		if ( ! $removed ) {
			wp_send_json_error(
				[
					'product_title' => get_the_title( $product_id ),
					'message'       => __( 'couldn\'t be removed.', 'betterwishlist' ),
				]
			);
		}

		wp_send_json_success(
			[
				'product_title' => get_the_title( $product_id ),
				'message'       => __( 'removed from wishlist.', 'betterwishlist' ),
			]
		);
	}

	/**
	 * ajax_add_to_cart_single
	 * All product in wishlist will be added after click Add to cart in wishlist archive page/ card page
	 *
	 * @return void
	 * @throws Exception When the received parameter is not of the expected input type.
	 * @since 1.0.0
	 */
	public function ajax_add_to_cart_single() {
		check_ajax_referer( 'better_wishlist_nonce', 'security' );

		if ( empty( $_REQUEST['product_id'] ) ) {
			wp_send_json_error(
				[
					'product_title' => '',
					'message'       => __( 'Product ID is should not be empty.', 'betterwishlist' ),
				]
			);
		}

		$product_id = intval( $_REQUEST['product_id'] );
		$product    = wc_get_product( $product_id );

		// add to cart
		if ( $product->is_type( 'variable' ) ) {
			$add_to_cart = WC()->cart->add_to_cart( $product_id, 1, $product->get_default_attributes() );
		} else {
			$add_to_cart = WC()->cart->add_to_cart( $product_id, 1 );
		}

		if ( $add_to_cart ) {
			if ( 'yes' === $this->settings['remove_from_wishlist'] ) {
				Plugin::instance()->model->delete_item( $product_id );
			}

			wp_send_json_success(
				[
					'product_title' => get_the_title( $product_id ),
					'message'       => __( 'added in cart.', 'betterwishlist' ),
				]
			);
		}

		wp_send_json_error(
			[
				'product_title' => get_the_title( $product_id ),
				'message'       => __( 'couldn\'t be added in cart.', 'betterwishlist' ),
			]
		);
	}

	/**
	 * ajax_add_to_cart_multiple
	 *
	 * @return void
	 * @throws Exception When the received parameter is not of the expected input type.
	 * @since 1.0.0
	 */
	public function ajax_add_to_cart_multiple() {
		check_ajax_referer( 'better_wishlist_nonce', 'security' );

		if ( empty( $_REQUEST['products'] ) ) {
			wp_send_json_error(
				[
					'product_title' => '',
					'message'       => __( 'Product ID is should not be empty.', 'betterwishlist' ),
				]
			);
		}

		foreach ( $_REQUEST['products'] as $product_id ) {
			WC()->cart->add_to_cart( $product_id, 1 );

			if ( filter_var( $this->settings['remove_from_wishlist'], FILTER_VALIDATE_BOOLEAN ) ) {
				Plugin::instance()->model->delete_item( $product_id );
			}
		}

		wp_send_json_success(
			[
				'product_title' => __( 'All items', 'betterwishlist' ),
				'message'       => __( 'added in cart.', 'betterwishlist' ),
			]
		);
	}
}
