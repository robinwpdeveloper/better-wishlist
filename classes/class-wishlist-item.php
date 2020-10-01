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

        public function add($item, $wishlist_id)
        {
            global $wpdb;

            if (empty($item) || empty($wishlist_id)) {
                return false;
            }

            $columns = [
                'prod_id' => '%d',
                'quantity' => '%d',
                'wishlist_id' => '%d',
                'position' => '%d',
                'original_price' => '%d',
                'original_currency' => '%s',
                'on_sale' => '%s',
                'user_id'   => '%d'
            ];

            $product = wc_get_product( $item['product_id'] );

            $values = [
                $item['product_id'],
                1,
                $wishlist_id,
                0,
                $product->get_sale_price(),
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
                return $wpdb->insert_id;
            }
            
            return false;
        }
    }
}

function Wishlist_Item()
{
    return Wishlist_Item::get_instance();
}
