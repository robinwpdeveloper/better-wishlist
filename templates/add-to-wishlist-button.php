<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

global $product;
?>

<div class="wishlist-wcwl-add-button">
	<a href="<?php echo esc_url( add_query_arg( 'add_to_wishlist', $product_id, $base_url ) ); ?>" rel="nofollow" data-product-id="<?php echo esc_attr( $product_id ) ?>" data-product-type="<?php echo esc_attr( $product_type ); ?>" data-original-product-id="<?php echo esc_attr( $parent_product_id ); ?>" class="<?php echo esc_attr( $link_classes ); ?>" data-title="<?php echo esc_attr( apply_filters( 'wishlist_add_to_wishlist_title', $label ) ); ?>">
		<?php echo $icon; ?>
		<span><?php echo wp_kses_post( $label ); ?></span>
	</a>
</div>