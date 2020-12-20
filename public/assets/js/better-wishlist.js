(function ($) {
	// create notification
	function createNotification(cssClass, response) {
		var message = response.product_title
			? `<strong>${response.product_title}</strong> ${response.message}`
			: response.message;
		var template = `<div class="better-wishlist-notification ${cssClass}">
			<p class="message">${message}</p>
		</div>`;

		// insert
		document.body.insertAdjacentHTML("beforeend", template);

		// remove
		setTimeout(() => {
			document.querySelector(".better-wishlist-notification").remove();
		}, 3000);
	}

	// create notice
	function createNotice(cssClass, response) {
		var wishlistTable = document.querySelector(".wishlist_table");
		var noticeWrapper = document.querySelector(".woocommerce-notices-wrapper");
		var message = response.product_title
			? `<strong>${response.product_title}</strong> ${response.message}`
			: response.message;
		var template = `<div class="woocommerce-notices-wrapper">
			<div class="woocommerce-message" role="alert">
				${message}
			</div>
		</div>`;

		if (noticeWrapper) {
			noticeWrapper.remove();
		}

		wishlistTable.insertAdjacentHTML("beforebegin", template);
	}

	$(document).ready(function () {
		// add to wishlist
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
					if (response.success) {
						if (BETTER_WISHLIST.settings.redirect_to_cart_page !== false) {
							window.location.replace(BETTER_WISHLIST.settings.cart_page_url); // set wishlist page here
						} else {
							createNotification("success", response.data);
						}
					} else {
						createNotification("error", response.data);
					}
				},
				error: function (response) {
					console.log(response);
				},
			});
		});

		// remove from wishlist
		$(document).on("click", ".remove_from_wishlist", function (e) {
			e.preventDefault();

			// var $this = $(this),
			// 	$product_id = $this.data("product_id"),
			// 	product_row = "#wishlist-row-" + $product_id,
			// 	wishlist_table = $(".wishlist_table");
			var productID = $(this).data("product_id");
			var table = $(".wishlist_table");
			var productRow = $("#wishlist-row-" + productID, table);

			$.ajax({
				type: "POST",
				url: BETTER_WISHLIST.ajax_url,
				data: {
					action: BETTER_WISHLIST.actions.remove_from_wishlist,
					security: BETTER_WISHLIST.nonce,
					product_id: productID,
				},
				success: function (response) {
					if (response.success) {
						productRow.remove();

						createNotice("error", response.data);

						if ($("tr.wishlist-row", table).length < 1) {
							$(".multiple-products-add-to-cart", table).remove();
							table.remove();
						}
					}
				},
				error: function (response) {
					console.log(response);
				},
			});
		});

		// add to cart
		$(document).on("click", ".single-product-add-to-cart", function (e) {
			e.preventDefault();

			var productID = $(this).data("product_id");
			var table = $(".wishlist_table");
			var productRow = $("#wishlist-row-" + productID, table);

			$.ajax({
				type: "POST",
				url: BETTER_WISHLIST.ajax_url,
				data: {
					action: BETTER_WISHLIST.actions.single_product_add_to_cart,
					security: BETTER_WISHLIST.nonce,
					product_id: productID,
				},
				success: function (response) {
					if (response.success) {
						if (BETTER_WISHLIST.settings.remove_from_wishlist) {
							productRow.remove();
						}

						if ($("tr.wishlist-row", table).length < 1) {
							$(".multiple-products-add-to-cart", table).remove();
							table.remove();
						}

						if (BETTER_WISHLIST.settings.redirect_to_cart_page !== false) {
							window.location.replace(BETTER_WISHLIST.settings.cart_page_url);
						} else {
							createNotification("success", response.data);
						}
					} else {
						createNotification("error", response.data);
					}
				},
				error: function (response) {
					console.log(response);
				},
			});
		});

		// add to cart - multiple
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
	});
})(jQuery);
