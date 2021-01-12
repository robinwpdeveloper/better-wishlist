const { __ } = wp.i18n;
const { TabPanel, Button } = wp.components;
const { Component, Fragment } = wp.element;

import GeneralSettings from "./tabs/general";
import ButtonSettings from "./tabs/button";
import CustomTextSettings from "./tabs/text";
import StyleSettings from "./tabs/style";

class Page extends Component {
	constructor() {
		super(...arguments);

		this.state = {
			wishlist_page: null,
			wishlist_menu: "yes",
			redirect_to_wishlist: "no",
			redirect_to_cart: "no",
			remove_from_wishlist: "yes",
			show_in_loop: "yes",
			position_in_loop: "after_add_to_cart",
			position_in_single: "after_add_to_cart",
			add_to_wishlist_text: __("Add to wishlist"),
			add_to_cart_text: __("Add to cart"),
			add_all_to_cart_text: __("Add all to cart"),
			wishlist_button_style: "default",
			wishlist_button_color: "#ffffff",
			wishlist_button_background: "default",
			wishlist_button_hover_color: "default",
			wishlist_button_hover_background: "default",
			wishlist_button_border_style: "none",
			wishlist_button_border_width: 1,
			wishlist_button_border_color: "default",
			wishlist_button_padding_top: 0,
			wishlist_button_padding_right: 0,
			wishlist_button_padding_bottom: 0,
			wishlist_button_padding_left: 0,
			cart_button_style: "default",
			cart_button_color: "#ffffff",
			cart_button_background: "default",
			cart_button_hover_color: "default",
			cart_button_hover_background: "default",
			cart_button_border_style: "none",
			cart_button_border_width: 1,
			cart_button_border_color: "default",
			cart_button_padding_top: 0,
			cart_button_padding_right: 0,
			cart_button_padding_bottom: 0,
			cart_button_padding_left: 0,
		};
	}

	componentDidMount() {
		this.setState({
			...this.state,
			...BetterWishlist.settings,
		});

		// get wp page list
		const { localStorage } = window;

		wp.apiFetch({ path: "/wp/v2/pages?per_page=-1" }).then((pages) => {
			if (pages.length > 0) {
				let opts = [];

				pages.map((page) => {
					opts.push({
						label: page.title.rendered,
						value: page.id,
					});
				});

				localStorage.setItem("bwpOpts", JSON.stringify(opts));
			}
		});
	}

	onChange(newState) {
		this.setState({
			...this.state,
			...newState,
		});
	}

	saveForm(ev) {
		const settings = this.state;

		jQuery.ajax({
			type: "POST",
			url: BetterWishlist.ajaxurl,
			data: {
				action: "bw_save_settings",
				security: BetterWishlist.nonce,
				settings,
			},
			success: function (response) {
				console.log(response);
			},
			error: function (response) {
				console.log(response);
			},
		});
	}

	render() {
		return (
			<Fragment>
				<div className="bw-settings-header">
					<h2 className="bw-settings-header-title">
						{__("BetterWishlist Settings")}
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
								return (
									<GeneralSettings
										state={this.state}
										onChange={this.onChange.bind(this)}
									/>
								);
							} else if (tab.name == "button") {
								return (
									<ButtonSettings
										state={this.state}
										onChange={this.onChange.bind(this)}
									/>
								);
							} else if (tab.name == "custom-text") {
								return (
									<CustomTextSettings
										state={this.state}
										onChange={this.onChange.bind(this)}
									/>
								);
							} else if (tab.name == "style") {
								return (
									<StyleSettings
										state={this.state}
										onChange={this.onChange.bind(this)}
									/>
								);
							}
						}}
					</TabPanel>

					<div className="bw-settings-footer">
						<Button onClick={this.saveForm.bind(this)}>{__("Save")}</Button>
					</div>
				</div>
			</Fragment>
		);
	}
}

export default Page;
