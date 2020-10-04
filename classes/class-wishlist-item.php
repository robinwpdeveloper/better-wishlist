<?php

if (!defined('ABSPATH')) {
    exit;
} // Exit if accessed directly

if ( !class_exists( 'Wishlist_Item' ) ) {

    class Wishlist_Item
    {

        protected static $instance;

        public static function get_instance()
        {
            if (is_null(self::$instance)) {
                self::$instance = new self();
            }

            return self::$instance;
        }

        public function get_product_price( $product ) {

			if ( ! $product ) {
				return 0;
			}

			switch ( $product->get_type() ) {
				case 'variable':
					return $product->get_variation_price( 'min' );
				default:
					$sale_price = $product->get_sale_price();
					return $sale_price ? $sale_price : $product->get_price();
			}
		}

        public function add($item, $wishlist_id)
        {
            global $wpdb;

            if (empty($item) || empty($wishlist_id)) {
                return false;
            }

            $columns = [
                'product_id' => '%d',
                'quantity' => '%d',
                'wishlist_id' => '%d',
                'position' => '%d',
                'original_price' => '%d',
                'original_currency' => '%s',
                'on_sale' => '%s',
                'user_id'   => '%d'
            ];

            $product = wc_get_product( $item['product_id'] );
            $product_price = $this->get_product_price( $product );


            $values = [
                $item['product_id'],
                1,
                $wishlist_id,
                0,
                $product_price,
                get_woocommerce_currency(),
                $product->is_on_sale(),
                get_current_user_id()
            ];

            $columns['dateadded'] = 'FROM_UNIXTIME( %d )';
            $values[] = current_time('timestamp');

            $query_columns = implode(', ', array_map('esc_sql', array_keys($columns)));
            $query_values = implode(', ', array_values($columns));
        
            $query = "INSERT INTO {$wpdb->ea_wishlist_items} ( {$query_columns} ) VALUES ( {$query_values} ) ";

            $res = $wpdb->query($wpdb->prepare($query, $values));

            if( $res ) {
                return apply_filters( 'wishlist_item_added_successfully', $wpdb->insert_id );
            }

            return false;
        }

        public function get_items($wishlist_id) {
            if( empty($wishlist_id) ) {
                return;
            }

            global $wpdb;
            $query = "SELECT * FROM {$wpdb->ea_wishlist_items} WHERE wishlist_id = {$wishlist_id}";
            $res = $wpdb->get_results($query, OBJECT);

            return $res;
        }

        public function remove($product_id)
        {
            if( empty($product_id) ){
                return false;
            }

            global $wpdb;

            // var_dump($wpdb->ea_wishlist_items);

            $res = $wpdb->delete($wpdb->ea_wishlist_items, ['product_id' => $product_id], ['%d']);

            // error_log(print_r($res, 1));

        }
    }
}

function Wishlist_Item()
{
    return Wishlist_Item::get_instance();
}
