<?php

/**
 * The Template for displaying add to wishlist product button.
 *
 *
 * @version             1.0.0
 * @package           Wishlist\Template
 */

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

global $product;

?>

<div class="wishlist-add-to-wishlist add-to-wishlist-<?php echo esc_attr($product_id); ?> <?php echo esc_attr($container_classes); ?> wishlist-fragment on-first-load" data-fragment-ref="<?php echo esc_attr($product_id); ?>" data-fragment-options="<?php echo esc_attr(json_encode($atts)); ?>">

	<div class="wishlist-add-button">
		<?php
		if ($atts['found_in_list'] != true) {
			do_action('better_wishlist_addtowishlist_button', ['product_id' => $product_id, 'product_type' => $product_type]);
		} else {
			printf('%s <a class="button" href="%s">%s</a>', $atts['already_in_wishlist_text'], $atts['wishlist_url'], $atts['browse_wishlist_text']);
		}
		?>
	</div>
</div>
