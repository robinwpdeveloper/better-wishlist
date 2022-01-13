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
	}

	updateValue(value) {
		this.props.onChange(value);
	}

	render() {
		const { state } = this.props;

		return (
			<Fragment>
				<BaseControl
					id="add-to-wishlist-text"
					label="Add to wishlist"
					help="Change button text">
					<TextControl
						value={state.add_to_wishlist_text}
						onChange={(value) =>
							this.updateValue({
								add_to_wishlist_text: value,
							})
						}
					/>
				</BaseControl>

				<BaseControl
					id="add-to-cart-text"
					label="Add to cart"
					help="Change button text">
					<TextControl
						value={state.add_to_cart_text}
						onChange={(value) =>
							this.updateValue({
								add_to_cart_text: value,
							})
						}
					/>
				</BaseControl>

				<BaseControl
					id="add-all-to-cart-text"
					label="Add all to cart"
					help="Change button text">
					<TextControl
						value={state.add_all_to_cart_text}
						onChange={(value) =>
							this.updateValue({
								add_all_to_cart_text: value,
							})
						}
					/>
				</BaseControl>
			</Fragment>
		);
	}
}

export default CustomTextSettings;
