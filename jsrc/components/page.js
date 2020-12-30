const { __ } = wp.i18n;

const {
	BaseControl,
	Button,
	PanelBody,
	PanelRow,
	ToggleControl,
	TextControl,
	ColorPicker,
} = wp.components;

const { Component, Fragment } = wp.element;

import Color from "./controls/color";

class Page extends Component {
	constructor() {
		super(...arguments);

		this.state = {
			tab: "general",
		};
	}

	render() {
		const { tab } = this.state;

		return (
			<Fragment>
				<div className="better-wishlist-admin-header">
					<h2 className="better-wishlist-admin-header-title">
						{__("Better Wishlist Settings")}
					</h2>
				</div>

				<div className="better-wishlist-admin-content">
					<PanelBody title={__("General")}>
						<PanelRow>
							<BaseControl
								id="redirect-to-wishlist"
								label="Redirect to wishlist"
								help="Redirect to wishlist page after adding a product to wishlist."
							>
								<ToggleControl
									checked={false}
									onChange={() => console.log("")}
								/>
							</BaseControl>
						</PanelRow>
						<PanelRow>
							<BaseControl
								id="remove-from-wishlist"
								label="Remove from wishlist"
								help="Remove from wishlist after adding a product to cart."
							>
								<ToggleControl
									checked={false}
									onChange={() => console.log("")}
								/>
							</BaseControl>
						</PanelRow>
						<PanelRow>
							<BaseControl
								id="redirect-to-cart"
								label="Redirect to cart"
								help="Redirect to cart page after adding a product to cart."
							>
								<ToggleControl
									checked={false}
									onChange={() => console.log("")}
								/>
							</BaseControl>
						</PanelRow>
					</PanelBody>

					<PanelBody title={__("Button Text")} initialOpen={false}>
						<PanelRow>
							<BaseControl id="add-to-cart-text" label="Add to cart">
								<TextControl
									value="Hello"
									onChange={(value) => console.log(value)}
								/>
							</BaseControl>
						</PanelRow>
						<PanelRow>
							<BaseControl id="add-to-wishlist-text" label="Add to wishlist">
								<TextControl
									value="Hello"
									onChange={(value) => console.log(value)}
								/>
							</BaseControl>
						</PanelRow>
						<PanelRow>
							<BaseControl
								id="add-all-to-wishlist-text"
								label="Add all to wishlist"
							>
								<TextControl
									value="Hello"
									onChange={(value) => console.log(value)}
								/>
							</BaseControl>
						</PanelRow>
					</PanelBody>

					<PanelBody title={__("Style")} initialOpen={false}>
						<PanelRow>
							<BaseControl id="button-color" label="Button color">
								<Color
									value="#dd102d"
									onChange={(value) => console.log(value)}
								/>
							</BaseControl>
						</PanelRow>
					</PanelBody>
				</div>
			</Fragment>
		);
	}
}

export default Page;
