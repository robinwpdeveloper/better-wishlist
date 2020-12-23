(function ($) {
	// create notification
	function createNotification(cssClass, response) {
		var uid = Math.random().toString(36).substr(2, 9);
		var message = response.product_title
			? `<strong>${response.product_title}</strong> ${response.message}`
			: response.message;
		var template = `<div class="better-wishlist-notification notification-${uid} ${cssClass}">
			<p class="message">${message}</p>
		</div>`;

		// insert
		if (document.querySelector(".better-wishlist-notification-wrap") === null) {
			document.body.insertAdjacentHTML(
				"beforeend",
				`<div class="better-wishlist-notification-wrap"></div>`
			);
		}

		document
			.querySelector(".better-wishlist-notification-wrap")
			.insertAdjacentHTML("beforeend", template);

		// remove
		setTimeout(() => {
			document.querySelector(".notification-" + uid).remove();
		}, 3500);
	}

	// create notice
	function createNotice(response) {
		var pageWrap = document.querySelector(".better-wishlist-page-wrap");
		var noticeWrap = document.querySelector(".woocommerce-notices-wrapper");
		var message = response.product_title
			? `<strong>${response.product_title}</strong> ${response.message}`
			: response.message;
		var template = `<div class="woocommerce-notices-wrapper">
			<div class="woocommerce-message" role="alert">
				${message}
			</div>
		</div>`;

		if (noticeWrap) {
			noticeWrap.remove();
		}

		pageWrap.insertAdjacentHTML("beforebegin", template);
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

			var productID = $(this).data("product_id");
			var pageWrap = $('.better-wishlist-page-wrap');
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
						createNotice(response.data);

						productRow.remove();

						if ($("tr.wishlist-row", table).length < 1) {
							$(".add-to-cart-multiple").remove();
							table.remove();
							pageWrap.html('<div class="no-record-message">No Records Found</div>');
						}
					} else {
						createNotice(response.data);
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
			var pageWrap = $('.better-wishlist-page-wrap');
			var table = $(".wishlist_table");
			var productRow = $("#wishlist-row-" + productID, table);

			$.ajax({
				type: "POST",
				url: BETTER_WISHLIST.ajax_url,
				data: {
					action: BETTER_WISHLIST.actions.add_to_cart_single,
					security: BETTER_WISHLIST.nonce,
					product_id: productID,
				},
				success: function (response) {
					if (response.success) {
						if (BETTER_WISHLIST.settings.remove_from_wishlist) {
							productRow.remove();

							if ($("tr.wishlist-row", table).length < 1) {
								$(".add-to-cart-multiple").remove();
								table.remove();
								pageWrap.html('<div class="no-record-message">No Records Found</div>');
							}
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
		$(document).on("click", ".bw-add-to-cart-multiple", function (e) {
			e.preventDefault();

			var products = $(this).data("products").split(",");

			$.ajax({
				type: "POST",
				url: BETTER_WISHLIST.ajax_url,
				data: {
					action: BETTER_WISHLIST.actions.add_to_cart_multiple,
					security: BETTER_WISHLIST.nonce,
					products: products,
				},
				success: function (response) {
					if (response.success) {
						if (BETTER_WISHLIST.settings.remove_from_wishlist) {
							$(".wishlist_table").remove();
							$(".add-to-cart-multiple").remove();
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
	});
})(jQuery);
