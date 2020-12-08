<?php

namespace BetterWishlist;

// If this file is called directly, abort.
if (!defined('ABSPATH')) {
    die;
}

class Helper
{
    private function __construct()
    {

    }

    public function get_product_price($product)
    {
        if (!$product) {
            return 0;
        }

        switch ($product->get_type()) {
            case 'variable':
                return $product->get_variation_price('min');
            default:
                $sale_price = $product->get_sale_price();
                return $sale_price ? $sale_price : $product->get_price();
        }
    }

    public function is_already_in_wishlist($product_id, $wishlist_id = null)
    {
        global $wpdb;

        if (empty($product_id)) {
            return false;
        }

        $product_id = sanitize_text_field($product_id);

        if (is_user_logged_in()) {
            $user_id = get_current_user_id();
            $result = $wpdb->get_row("SELECT * FROM {$wpdb->better_wishlist_items} WHERE user_id = {$user_id} AND product_id = {$product_id}");
        } else {
            $result = $wpdb->get_row("SELECT * FROM {$wpdb->better_wishlist_items} WHERE wishlist_id = '{$wishlist_id}' and product_id = {$product_id}");
        }

        return !empty($result) ? true : false;
    }

}
