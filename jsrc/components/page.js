const { __ } = wp.i18n;

const { TabPanel } = wp.components;

const { Component, Fragment } = wp.element;

import GeneralSettings from "./tabs/general";
import ButtonSettings from "./tabs/button";
import CustomTextSettings from "./tabs/text";
import StyleSettings from "./tabs/style";

class Page extends Component {
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
				<div className="bw-settings-header">
					<h2 className="bw-settings-header-title">
						{__("Better Wishlist Settings")}
					</h2>
				</div>

				<div className="bw-settings-content">
					<TabPanel
						tabs={[
							{
								name: "general",
								title: __("General"),
							},
							{
								name: "button",
								title: __("Button"),
							},
							{
								name: "custom-text",
								title: __("Custom Text"),
							},
							{
								name: "style",
								title: __("Style"),
							},
						]}
						initialTabName="general"
					>
						{(tab) => {
							if (tab.name == "general") {
								return <GeneralSettings />;
							} else if (tab.name == "button") {
								return <ButtonSettings />;
							} else if (tab.name == "custom-text") {
								return <CustomTextSettings />;
							} else if (tab.name == "style") {
								return <StyleSettings />;
							}
						}}
					</TabPanel>
				</div>
			</Fragment>
		);
	}
}

export default Page;
