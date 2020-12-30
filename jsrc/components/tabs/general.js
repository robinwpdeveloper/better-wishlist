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

class GeneralSettings extends Component {
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
				<BaseControl
					id="redirect-to-wishlist"
					label="Redirect to wishlist"
					help="Redirect to wishlist page after adding a product to wishlist."
				>
					<ToggleControl
						checked={this.state.redirect_to_wishlist}
						onChange={() =>
							this.setState({
								redirect_to_wishlist: !this.state.redirect_to_wishlist,
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
						checked={this.state.redirect_to_cart}
						onChange={() =>
							this.setState({
								redirect_to_cart: !this.state.redirect_to_cart,
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
						checked={this.state.remove_from_wishlist}
						onChange={() =>
							this.setState({
								remove_from_wishlist: !this.state.remove_from_wishlist,
							})
						}
					/>
				</BaseControl>
			</Fragment>
		);
	}
}

export default GeneralSettings;
