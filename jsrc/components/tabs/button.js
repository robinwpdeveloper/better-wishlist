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

class ButtonSettings extends Component {
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
					id="show-in-loop"
					label="Show in loop"
					help="Show wishlist button in product loop."
				>
					<ToggleControl
						checked={this.state.show_in_loop}
						onChange={() =>
							this.setState({
								show_in_loop: !this.state.show_in_loop,
							})
						}
					/>
				</BaseControl>

				<BaseControl id="loop-position" label="Position in loop">
					<SelectControl
						options={[
							{
								value: "before_cart",
								label: "Before add to cart",
							},
							{
								value: "after_cart",
								label: "After add to cart",
							},
						]}
						value={this.state.position_in_loop}
						onChange={(value) =>
							this.setState({
								position_in_loop: value,
							})
						}
					/>
				</BaseControl>

				<BaseControl id="single-position" label="Position in product page">
					<SelectControl
						options={[
							{
								value: "before_cart",
								label: "Before add to cart",
							},
							{
								value: "after_cart",
								label: "After add to cart",
							},
						]}
						value={this.state.position_in_single}
						onChange={(value) =>
							this.setState({
								position_in_single: value,
							})
						}
					/>
				</BaseControl>
			</Fragment>
		);
	}
}

export default ButtonSettings;
