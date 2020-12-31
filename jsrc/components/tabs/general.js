const { __ } = wp.i18n;
const { Component, Fragment } = wp.element;
const { BaseControl, ToggleControl } = wp.components;

import Select from "./../controls/select";

class GeneralSettings extends Component {
	constructor() {
		super(...arguments);

		// this.state = {
		// 	redirect_to_wishlist: false,
		// 	redirect_to_cart: false,
		// 	remove_from_wishlist: true,
		// 	show_in_loop: true,
		// 	position_in_loop: "after_cart",
		// 	position_in_single: "after_cart",
		// 	add_to_wishlist_text: __("Add to wishlist"),
		// 	add_to_cart_text: __("Add to cart"),
		// 	add_all_to_wishlist_text: __("Add all to cart"),
		// };
	}

	updateValue(value) {
		this.props.onChange(value);
	}

	render() {
		const { state } = this.props;

		return (
			<Fragment>
				<BaseControl
					id="wishlist-page"
					label="Wishlist page"
					help={__(
						"Pick a page as the main Wishlist page; make sure you add the [better_wishlist] shortcode into the page content."
					)}
				>
					<Select />
				</BaseControl>

				<BaseControl
					id="wishlist-menu"
					label="Wishlist menu"
					help={__("Add wishlist menu in 'my account' panel.")}
				>
					<ToggleControl
						checked={state.wishlist_menu}
						onChange={() =>
							this.updateValue({
								wishlist_menu: !state.wishlist_menu,
							})
						}
					/>
				</BaseControl>

				<BaseControl
					id="redirect-to-wishlist"
					label="Redirect to wishlist"
					help="Redirect to wishlist page after adding a product to wishlist."
				>
					<ToggleControl
						checked={state.redirect_to_wishlist}
						onChange={() =>
							this.updateValue({
								redirect_to_wishlist: !state.redirect_to_wishlist,
							})
						}
					/>
				</BaseControl>

				<BaseControl
					id="redirect-to-cart"
					label="Redirect to cart"
					help="Redirect to cart page after adding a product to cart."
				>
					<ToggleControl
						checked={state.redirect_to_cart}
						onChange={() =>
							this.updateValue({
								redirect_to_cart: !state.redirect_to_cart,
							})
						}
					/>
				</BaseControl>

				<BaseControl
					id="remove-from-wishlist"
					label="Remove from wishlist"
					help="Remove from wishlist after adding a product to cart."
				>
					<ToggleControl
						checked={state.remove_from_wishlist}
						onChange={() =>
							this.updateValue({
								remove_from_wishlist: !state.remove_from_wishlist,
							})
						}
					/>
				</BaseControl>
			</Fragment>
		);
	}
}

export default GeneralSettings;
