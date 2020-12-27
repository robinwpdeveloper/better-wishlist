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
		$(document).on("click", ".better-wishlist-add-to-wishlist", function (e) {
			e.preventDefault();

			var productID = $(this).data("product-id");

			$.ajax({
				type: "POST",
				url: BETTER_WISHLIST.ajax_url,
				data: {
					action: BETTER_WISHLIST.actions.add_to_wishlist,
					security: BETTER_WISHLIST.nonce,
					product_id: productID,
				},
				success: function (response) {
					if (response.success) {
						if (BETTER_WISHLIST.settings.redirect_to_wishlist !== false) {
							window.location.replace(
								BETTER_WISHLIST.settings.wishlist_page_url
							);
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
		$(document).on("click", ".better-wishlist-remove-from-wishlist", function (e) {
			e.preventDefault();

			var pageWrap = $(".better-wishlist-page-wrap");
			var table = $(".wishlist_table");
			var productID = $(this).data("product_id");
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
							pageWrap.empty();
							pageWrap.html(
								'<div class="no-record-message">No Records Found</div>'
							);
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
		$(document).on("click", ".better-wishlist-add-to-cart-single", function (e) {
			e.preventDefault();

			var pageWrap = $(".better-wishlist-page-wrap");
			var table = $(".wishlist_table");
			var productID = $(this).data("product_id");
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
						if (BETTER_WISHLIST.settings.redirect_to_cart !== false) {
							window.location.replace(BETTER_WISHLIST.settings.cart_page_url);
						} else {
							createNotification("success", response.data);

							if (BETTER_WISHLIST.settings.remove_from_wishlist) {
								productRow.remove();

								if ($("tr.wishlist-row", table).length < 1) {
									pageWrap.empty();
									pageWrap.html(
										'<div class="no-record-message">No Records Found</div>'
									);
								}
							}
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
		$(document).on("click", ".better-wishlist-add-to-cart-multiple", function (e) {
			e.preventDefault();

			var pageWrap = $(".better-wishlist-page-wrap");
			var products = $(this).data("products").toString().split(",");

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
						if (BETTER_WISHLIST.settings.redirect_to_cart !== false) {
							window.location.replace(BETTER_WISHLIST.settings.cart_page_url);
						} else {
							createNotification("success", response.data);

							if (BETTER_WISHLIST.settings.remove_from_wishlist) {
								pageWrap.empty();
								pageWrap.html(
									'<div class="no-record-message">No Records Found</div>'
								);
							}
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
