const { __ } = wp.i18n;

const { BaseControl, Text } = wp.components;

const { Component, Fragment } = wp.element;

import Color from "./../controls/color";

class StyleSettings extends Component {
	constructor() {
		super(...arguments);

		this.state = {
			redirect_to_wishlist: false,
			redirect_to_cart: false,
			remove_from_wishlist: true,
			show_in_loop: true,
			position_in_loop: "after_cart",
			position_in_single: "after_cart",
			add_to_wishlist_text: __("Add to wishlist"),
			add_to_cart_text: __("Add to cart"),
			add_all_to_wishlist_text: __("Add all to cart"),
		};
	}

	render() {
		return (
			<Fragment>
				<h3>{__("Wishlist Button")}</h3>

				<BaseControl
					id="wishlist-button-color"
					label="Color"
				>
					<Color value="#cd2122" onChange={(value) => console.log(value)} />
				</BaseControl>

				<BaseControl
					id="wishlist-button-color-hover"
					label="Color hover"
				>
					<Color value="#cd2122" onChange={(value) => console.log(value)} />
				</BaseControl>

                <h3>{__("Cart Button")}</h3>

                <BaseControl
					id="cart-button-color"
					label="Color"
				>
					<Color value="#cd2122" onChange={(value) => console.log(value)} />
				</BaseControl>

				<BaseControl
					id="cart-button-color-hover"
					label="Color hover"
				>
					<Color value="#cd2122" onChange={(value) => console.log(value)} />
				</BaseControl>
			</Fragment>
		);
	}
}

export default StyleSettings;
