const { __ } = wp.i18n;

const {
	TabPanel,
	BaseControl,
	Button,
	PanelBody,
	PanelRow,
	ToggleControl,
	TextControl,
	ColorPicker,
	SelectControl,
} = wp.components;

const { Component, Fragment } = wp.element;

import Color from "./../controls/color";

class CustomTextSettings extends Component {
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
				<BaseControl id="add-to-wishlist-text" label="Add to wishlist">
					<TextControl
						value={this.state.add_to_wishlist_text}
						onChange={(value) =>
							this.setState({
								add_to_wishlist_text: value,
							})
						}
					/>
				</BaseControl>

				<BaseControl id="add-to-cart-text" label="Add to cart">
					<TextControl
						value={this.state.add_to_cart_text}
						onChange={(value) =>
							this.setState({
								add_to_cart_text: value,
							})
						}
					/>
				</BaseControl>

				<BaseControl id="add-all-to-cart-text" label="Add all to cart">
					<TextControl
						value={this.state.add_all_to_wishlist_text}
						onChange={(value) =>
							this.setState({
								add_all_to_wishlist_text: value,
							})
						}
					/>
				</BaseControl>
			</Fragment>
		);
	}
}

export default CustomTextSettings;
