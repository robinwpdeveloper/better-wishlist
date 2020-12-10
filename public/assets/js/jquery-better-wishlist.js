(function ($) {
	function wishlist_remove_modal() {
		$("body").find(".wishlist-modal-dialog-box").remove();
	}

	function wishlist_show_modal(data) {
		//console.log(data);
		let message = "";
		const wishListTable = document.querySelector(".wishlist_table");
		const noticeWrapper = document.querySelector(
			".woocommerce-notices-wrapper"
		);

		if (data.added_to_wishlist) {
			message = "Added to Wishlist";
			product_title = data.product_title;
		} else if (data.added_to_cart) {
			message = "Added to Cart";
			product_title = data.product_title;
		} else if (data.wishlist_removed) {
			message = '"' + data.product_title + '" Removed from Wishlist';
		} else {
			message = "Added to Cart";
			product_title = "All Products";
		}

		if (data.wishlist_removed) {
			let template = `<div class="woocommerce-notices-wrapper">
                       <div class="woocommerce-message" role="alert">
                         ${message}
                       </div>
                      </div>`;

			if (noticeWrapper) {
				noticeWrapper.remove();
			}
			wishListTable.insertAdjacentHTML("beforebegin", template);
		} else {
			let template = `<div class="better-wishlist-popup-area">
                        <div>
                          <div class="better-wishlist-popup">
                            <div class="toast-content">
                            <div class="before"></div>
                            <div class="icon">&#x2714</div>
                            <div class="text">
                              <p> ${product_title} </p>
                              <p class="message">${message}</p>
                            </div>
                          </div>
                        </div>
                      </div>`;
			document.body.insertAdjacentHTML("beforeend", template);
			setTimeout(() => {
				document.querySelector(".better-wishlist-popup-area").remove();
			}, 1000);
		}
	}

	$(document).ready(function () {
		$(document).on("click", ".add_to_wishlist_button", function (e) {
			e.preventDefault();

			var $this = $(this);

			$.ajax({
				type: "POST",
				url: BETTER_WISHLIST.ajax_url,
				data: {
					action: BETTER_WISHLIST.actions.add_to_wishlist,
					security: BETTER_WISHLIST.nonce,
					product_id: $this.data("product-id"),
				},
				success: function (response) {
					console.log(response);
					if (response.success) {
						if (response.data.redirects) {
							window.location.replace(data.fragments.wishlist_url);
						} else {
							wishlist_show_modal(response.data);
						}
					}
				},
				error: function (response) {
					console.log(response);
				},
			});
		});

		$(document).on("click", ".bw-multiple-products-add-to-carts", function (e) {
			e.preventDefault();

			var $product_ids = $(this).data("product-ids"),
				$product_ids = $product_ids.split(":");

			$.ajax({
				type: "POST",
				url: BETTER_WISHLIST.ajax_url,
				data: {
					_ajax_nonce: BETTER_WISHLIST.nonce,
					action: BETTER_WISHLIST.actions.multiple_product_add_to_cart,
					product_ids: $product_ids,
				},
				success: function (response) {
					if (response.success) {
						if (response.data.removed) {
							$(".wishlist_table").remove();
							$(".multiple-products-add-to-cart").remove();
						}

						if (response.data.redirects != null) {
							window.location.replace(response.data.redirects);
						} else {
							wishlist_show_modal(response.data);
						}
					}
					//console.log(response);
					// $('.wishlist_table').remove();
					// $('.multiple-products-add-to-cart').remove();
					// all_product_add_cart_modal();
				},
				error: function (response) {
					console.log(response);
				},
			});
		});

		$(document).on("click", ".remove_from_wishlist", function (e) {
			e.preventDefault();

			var $this = $(this),
				$product_id = $this.data("product_id"),
				product_row = "#wishlist-row-" + $product_id,
				wishlist_table = $(".wishlist_table");

			$.ajax({
				type: "POST",
				url: BETTER_WISHLIST.ajax_url,
				data: {
					_ajax_nonce: BETTER_WISHLIST.nonce,
					action: BETTER_WISHLIST.actions.remove_from_wishlist,
					product_id: $product_id,
				},
				success: function (response) {
					if (response.success) {
						$(product_row).remove();
						wishlist_show_modal(response.data);
					}
					if ($(".wishlist_table tr.wishlist-row").length < 1) {
						$(".multiple-products-add-to-cart").remove();
						$(".wishlist_table").remove();
					}
				},
				error: function (response) {
					console.log(response);
				},
			});
		});

		$(document).on("click", ".single-product-add-to-cart", function (e) {
			e.preventDefault();

			var $this = $(this),
				$product_id = $this.data("product_id");
			(product_row = "#wishlist-row-" + $product_id),
				(wishlist_table = $(".wishlist_table"));

			$.ajax({
				type: "POST",
				url: BETTER_WISHLIST.ajax_url,
				data: {
					_ajax_nonce: BETTER_WISHLIST.nonce,
					action: BETTER_WISHLIST.actions.single_product_add_to_cart,
					product_id: $product_id,
				},
				success: function (response) {
					console.log(response);

					if (response.success) {
						if (response.data.removed) {
							$(product_row).remove();
						}
						if ($(".wishlist_table tr.wishlist-row").length < 1) {
							$(".multiple-products-add-to-cart").remove();
							$(".wishlist_table").remove();
						}

						if (response.data.redirects != null) {
							window.location.replace(response.data.redirects);
						} else {
							wishlist_show_modal(response.data);
						}
					}
				},
				error: function (response) {
					console.log(response);
				},
			});
		});
	});
})(jQuery);
