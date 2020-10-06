<?php

/**
 * Add to wishlists shortcode and hooks
 *
 * @since             1.0.0
 * @package           Wishlist\Classes
 */

// If this file is called directly, abort.
if (!defined('ABSPATH')) {
    die;
}

if (!class_exists('Better_Wishlist_Shortcode')) {
    class Better_Wishlist_Shortcode
    {

        /**
         * This class
         *
         * @var \Wishlist
         */
        protected static $instance = null;

        /**
         * Get this class object
         *
         * @param string $plugin_name Plugin name.
         *
         * @return \Wishlist
         */
        public static function get_instance() {
            if ( is_null( self::$instance ) ) {
                self::$instance = new self();
            }

            return self::$instance;
        }

        public function __construct()
        {
            add_shortcode('better_wishlist_shortcode', [$this, 'wishlist']);
        }


        public function wishlist($atts, $content = null)
        {
            global $wpdb;

            $atts = shortcode_atts([
                'per_page'  => 5,
                'current_page'  => 1,
                'pagination'    => 'no',
                'layout'    => ''
            ], $atts);

            extract($atts);

            $items = Better_Wishlist_Item()->get_items(User_Wishlist()->get_current_user_wishlist());

            if( empty($items) ) return _e( 'No Records Found', 'better-wishlist' );

            ob_start();

            ?>
            <table class="shop_table cart wishlist_table wishlist_view traditional responsive   " data-pagination="no" data-per-page="5" data-page="1" data-id="3">
                <thead>
                    <tr>
                        <th class="product-remove">
                            <span class="nobr"></span>
                        </th>
                        <th class="product-thumbnail"></th>
                        <th class="product-name">
                            <span class="nobr"><?php _e('Product name', 'better-wishlist'); ?></span>
                        </th>
                    </tr>
                </thead>
                <tbody class="wishlist-items-wrapper">

                    <?php
                        foreach($items as $item) :

                        $product = wc_get_product($item->product_id);

                        if($product) {
                            
                    ?>
                        <tr id="wishlist-row-<?php echo $product->get_id(); ?>" data-row-id="<?php echo $product->get_id(); ?>">
                            <td class="product-remove">
                                <div>
                                <a href="/better_wishlist/?remove_from_wishlist=<?php echo $product->get_id(); ?>" class="remove remove_from_wishlist" title="<?php _e('Remove this product', 'better-wishlist'); ?>">×</a>
                                </div>
                            </td>
                            <td class="product-thumbnail">
                                <a href="<?php echo esc_url(get_permalink( $product->get_id() )); ?>"><img width="300" height="300" src="<?php echo esc_url(get_the_post_thumbnail_url($product->get_id())); ?>" class="attachment-woocommerce_thumbnail size-woocommerce_thumbnail" alt="" loading="lazy"></a>
                            </td>
                            <td class="product-name">
                                <a href="<?php echo esc_url(get_permalink( $product->get_id() )); ?>"><?php echo $product->get_title(); ?></a>
                            </td>
                        </tr>
                        <?php 
                        }
                    endforeach;
                ?>    
                </tbody>
                </table>
            <?php

            return ob_get_clean();
        }
    }
}

Better_Wishlist_Shortcode::get_instance();