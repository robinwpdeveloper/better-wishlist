(function ($) {
	var checkMark = `<?xml version="1.0" encoding="utf-8"?>
	<svg version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
		 viewBox="0 0 352.6 352.6" style="enable-background:new 0 0 352.6 352.6;" xml:space="preserve">
	<style type="text/css">
		.st0{fill:#FFFFFF;}
	</style>
	<g>
		<path class="st0" d="M337.2,23c-15.9-8.6-33.7,8-44.1,17.7c-23.9,23.3-44.1,50.2-66.7,74.7c-25.1,26.9-48.3,53.9-74.1,80.2
			c-14.7,14.7-30.6,30.6-40.4,49c-22-21.4-41-44.7-65.5-63.6C28.8,167.4-0.6,157.6,0,190c1.2,42.2,38.6,87.5,66.1,116.3
			c11.6,12.2,26.9,25.1,44.7,25.7c21.4,1.2,43.5-24.5,56.3-38.6c22.6-24.5,41-52,61.8-77.1c26.9-33,54.5-65.5,80.8-99.1
			C326.2,96.4,378.2,45,337.2,23z M26.9,187.6c-0.6,0-1.2,0-2.4,0.6c-2.4-0.6-4.3-1.2-6.7-2.4l0,0C19.6,184.5,22.7,185.1,26.9,187.6z
			"/>
	</g>
	</svg>
	`;
	// create notification
	function createNotification(cssClass, response) {
		var uid = Math.random().toString(36).substr(2, 9);
		var message = response.product_title
			? `<strong>${response.product_title}</strong> ${response.message}`
			: response.message;
		var template = `<div class="betterwishlist-notification notification-${uid} ${cssClass}">
			${cssClass == "success" ? checkMark : ""}
			<p class="message">${message}</p>
		</div>`;

		// insert
		if (
			document.querySelector(".betterwishlist-notification-wrap") === null
		) {
			document.body.insertAdjacentHTML(
				"beforeend",
				`<div class="betterwishlist-notification-wrap"></div>`
			);
		}

		document
			.querySelector(".betterwishlist-notification-wrap")
			.insertAdjacentHTML("beforeend", template);

		// remove
		setTimeout(() => {
			document.querySelector(".notification-" + uid).remove();
		}, 3500);
	}

	// create notice
	function createNotice(response) {
		var pageWrap = document.querySelector(".betterwishlist-page-wrap");
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
		$(document).on(
			"click",
			".betterwishlist-add-to-wishlist",
			function (e) {
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
							if (
								BETTER_WISHLIST.settings.redirect_to_wishlist ==
								"yes"
							) {
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
			}
		);

		// remove from wishlist
		$(document).on(
			"click",
			".betterwishlist-remove-from-wishlist",
			function (e) {
				e.preventDefault();

				var pageWrap = $(".betterwishlist-page-wrap");
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
									'<div class="no-record-message">' +
										BETTER_WISHLIST.i18n.no_records_found +
										"</div>"
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
			}
		);

		// add to cart
		$(document).on(
			"click",
			".betterwishlist-add-to-cart-single",
			function (e) {
				e.preventDefault();

				var pageWrap = $(".betterwishlist-page-wrap");
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
							if (
								BETTER_WISHLIST.settings.redirect_to_cart ==
								"yes"
							) {
								window.location.replace(
									BETTER_WISHLIST.settings.cart_page_url
								);
							} else {
								createNotification("success", response.data);

								if (
									BETTER_WISHLIST.settings
										.remove_from_wishlist
								) {
									productRow.remove();

									if (
										$("tr.wishlist-row", table).length < 1
									) {
										pageWrap.empty();
										pageWrap.html(
											'<div class="no-record-message">' +
												BETTER_WISHLIST.i18n
													.no_records_found +
												"</div>"
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
			}
		);

		// add to cart - multiple
		$(document).on(
			"click",
			".betterwishlist-add-to-cart-multiple",
			function (e) {
				e.preventDefault();

				var pageWrap = $(".betterwishlist-page-wrap");
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
							if (
								BETTER_WISHLIST.settings.redirect_to_cart ==
								"yes"
							) {
								window.location.replace(
									BETTER_WISHLIST.settings.cart_page_url
								);
							} else {
								createNotification("success", response.data);

								if (
									BETTER_WISHLIST.settings
										.remove_from_wishlist
								) {
									pageWrap.empty();
									pageWrap.html(
										'<div class="no-record-message">' +
											BETTER_WISHLIST.i18n
												.no_records_found +
											"</div>"
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
			}
		);
	});
})(jQuery);
