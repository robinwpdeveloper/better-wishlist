const { __ } = wp.i18n;

const { BaseControl, RangeControl, SelectControl } = wp.components;

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

				<BaseControl id="wishlist-button-style" label="Style">
					<SelectControl
						value="default"
						onChange={(value) => {
							console.log(value);
						}}
						options={[
							{ value: "default", label: "Theme Default" },
							{ value: "custom", label: "Custom" },
						]}
					/>
				</BaseControl>

				<BaseControl id="wishlist-button-color" label="Color">
					<Color value="#cd2122" onChange={(value) => console.log(value)} />
				</BaseControl>

				<BaseControl id="wishlist-button-color-hover" label="Color(hover)">
					<Color value="#cd2122" onChange={(value) => console.log(value)} />
				</BaseControl>

				<BaseControl id="wishlist-button-bg-color" label="Background Color">
					<Color value="#cd2122" onChange={(value) => console.log(value)} />
				</BaseControl>

				<BaseControl
					id="wishlist-button-bg-color-hover"
					label="Background Color(hover)"
				>
					<Color value="#cd2122" onChange={(value) => console.log(value)} />
				</BaseControl>

				<BaseControl id="wishlist-button-border-color" label="Border Color">
					<Color value="#cd2122" onChange={(value) => console.log(value)} />
				</BaseControl>

				<BaseControl
					id="wishlist-button-border-color-hover"
					label="Border Color(hover)"
				>
					<Color value="#cd2122" onChange={(value) => console.log(value)} />
				</BaseControl>

				<BaseControl id="wishlist-button-padding-top" label="Padding Top">
					<RangeControl
						value={10}
						onChange={(columns) => console.log(columns)}
						min={0}
						max={100}
					/>
				</BaseControl>

				<BaseControl id="wishlist-button-padding-right" label="Padding Right">
					<RangeControl
						value={10}
						onChange={(columns) => console.log(columns)}
						min={0}
						max={100}
					/>
				</BaseControl>

				<BaseControl id="wishlist-button-padding-bottom" label="Padding Bottom">
					<RangeControl
						value={10}
						onChange={(columns) => console.log(columns)}
						min={0}
						max={100}
					/>
				</BaseControl>

				<BaseControl id="wishlist-button-padding-left" label="Padding Left">
					<RangeControl
						value={10}
						onChange={(columns) => console.log(columns)}
						min={0}
						max={100}
					/>
				</BaseControl>

				<h3>{__("Cart Button")}</h3>

				<BaseControl id="cart-button-style" label="Style">
					<SelectControl
						value="default"
						onChange={(value) => {
							console.log(value);
						}}
						options={[
							{ value: "default", label: "Theme Default" },
							{ value: "custom", label: "Custom" },
						]}
					/>
				</BaseControl>

				<BaseControl id="cart-button-color" label="Color">
					<Color value="#cd2122" onChange={(value) => console.log(value)} />
				</BaseControl>

				<BaseControl id="cart-button-color-hover" label="Color(hover)">
					<Color value="#cd2122" onChange={(value) => console.log(value)} />
				</BaseControl>

				<BaseControl id="cart-button-bg-color" label="Background Color">
					<Color value="#cd2122" onChange={(value) => console.log(value)} />
				</BaseControl>

				<BaseControl
					id="cart-button-bg-color-hover"
					label="Background Color(hover)"
				>
					<Color value="#cd2122" onChange={(value) => console.log(value)} />
				</BaseControl>

				<BaseControl id="cart-button-border-color" label="Border Color">
					<Color value="#cd2122" onChange={(value) => console.log(value)} />
				</BaseControl>

				<BaseControl
					id="cart-button-border-color-hover"
					label="Border Color(hover)"
				>
					<Color value="#cd2122" onChange={(value) => console.log(value)} />
				</BaseControl>

				<BaseControl id="cart-button-padding-top" label="Padding Top">
					<RangeControl
						value={10}
						onChange={(columns) => console.log(columns)}
						min={0}
						max={100}
					/>
				</BaseControl>

				<BaseControl id="cart-button-padding-right" label="Padding Right">
					<RangeControl
						value={10}
						onChange={(columns) => console.log(columns)}
						min={0}
						max={100}
					/>
				</BaseControl>

				<BaseControl id="cart-button-padding-bottom" label="Padding Bottom">
					<RangeControl
						value={10}
						onChange={(columns) => console.log(columns)}
						min={0}
						max={100}
					/>
				</BaseControl>

				<BaseControl id="cart-button-padding-left" label="Padding Left">
					<RangeControl
						value={10}
						onChange={(columns) => console.log(columns)}
						min={0}
						max={100}
					/>
				</BaseControl>
			</Fragment>
		);
	}
}

export default StyleSettings;
