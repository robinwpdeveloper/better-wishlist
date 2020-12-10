<?php

namespace BetterWishlist;

// If this file is called directly, abort.
if (!defined('ABSPATH')) {
    die;
}

class Helper
{
    public function get_product_price($product)
    {
        if (!$product) {
            return false;
        }

        switch ($product->get_type()) {
            case 'variable':
                return $product->get_variation_price('min');
            default:
                $sale_price = $product->get_sale_price();
                return $sale_price ? $sale_price : $product->get_price();
        }
    }
}
