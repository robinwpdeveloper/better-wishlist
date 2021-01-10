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
	}

	updateValue(value) {
		this.props.onChange(value);
	}

	render() {
		const { state } = this.props;

		return (
			<Fragment>
				<BaseControl
					id="show-in-loop"
					label="Show in loop"
					help="Show wishlist button in product loop."
				>
					<ToggleControl
						checked={state.show_in_loop == "yes"}
						onChange={() =>
							this.updateValue({
								show_in_loop: state.show_in_loop == "yes" ? "no" : "yes",
							})
						}
					/>
				</BaseControl>

				{state.show_in_loop == "yes" && (
					<BaseControl id="loop-position" label="Position in loop">
						<SelectControl
							options={[
								{
									value: "before_add_to_cart",
									label: "Before add to cart",
								},
								{
									value: "after_add_to_cart",
									label: "After add to cart",
								},
							]}
							value={state.position_in_loop}
							onChange={(value) =>
								this.updateValue({
									position_in_loop: value,
								})
							}
						/>
					</BaseControl>
				)}

				<BaseControl id="single-position" label="Position in product page">
					<SelectControl
						options={[
							{
								value: "before_add_to_cart",
								label: "Before add to cart",
							},
							{
								value: "after_add_to_cart",
								label: "After add to cart",
							},
						]}
						value={state.position_in_single}
						onChange={(value) =>
							this.updateValue({
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
