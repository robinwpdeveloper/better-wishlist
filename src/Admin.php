<?php

namespace BetterWishlist;

// If this file is called directly, abort.
if (!defined('ABSPATH')) {
    die;
}

class Admin
{
    private $better_wishlist_options;

    public function __construct()
    {
        add_action('admin_menu', array($this, 'better_wishlist_add_plugin_page'));
        add_action('admin_init', array($this, 'better_wishlist_page_init'));
    }

    public function better_wishlist_add_plugin_page()
    {
        add_options_page(
            'Better Wishlist', // page_title
            'Better Wishlist', // menu_title
            'manage_options', // capability
            'better-wishlist', // menu_slug
            array($this, 'better_wishlist_create_admin_page') // function
        );
    }

    public function better_wishlist_create_admin_page()
    {
        $this->better_wishlist_options = get_option('better_wishlist_option_name');?>

		<div class="wrap">
			<h2>Better Wishlist</h2>
			<p></p>
			<?php settings_errors();?>

			<form method="post" action="options.php">
				<?php
settings_fields('better_wishlist_option_group');
        do_settings_sections('better-wishlist-admin');
        submit_button();
        ?>
			</form>
		</div>
	<?php }

    public function better_wishlist_page_init()
    {
        register_setting(
            'better_wishlist_option_group', // option_group
            'better_wishlist_option_name', // option_name
            array($this, 'better_wishlist_sanitize') // sanitize_callback
        );

        add_settings_section(
            'better_wishlist_setting_section', // id
            'Settings', // title
            array($this, 'better_wishlist_section_info'), // callback
            'better-wishlist-admin' // page
        );

        add_settings_field(
            'redirect_to_wishlist_page_0', // id
            'Redirect to wishlist page', // title
            array($this, 'redirect_to_wishlist_page_0_callback'), // callback
            'better-wishlist-admin', // page
            'better_wishlist_setting_section' // section
        );

        add_settings_field(
            'redirect_to_cart_page_1', // id
            'Redirect to cart page', // title
            array($this, 'redirect_to_cart_page_1_callback'), // callback
            'better-wishlist-admin', // page
            'better_wishlist_setting_section' // section
        );

        add_settings_field(
            'remove_from_wishlist_after_adding_to_cart_2', // id
            'Remove from wishlist after adding to cart', // title
            array($this, 'remove_from_wishlist_after_adding_to_cart_2_callback'), // callback
            'better-wishlist-admin', // page
            'better_wishlist_setting_section' // section
        );
    }

    public function better_wishlist_sanitize($input)
    {
        $sanitary_values = array();
        if (isset($input['redirect_to_wishlist_page_0'])) {
            $sanitary_values['redirect_to_wishlist_page_0'] = $input['redirect_to_wishlist_page_0'];
        }

        if (isset($input['redirect_to_cart_page_1'])) {
            $sanitary_values['redirect_to_cart_page_1'] = $input['redirect_to_cart_page_1'];
        }

        if (isset($input['remove_from_wishlist_after_adding_to_cart_2'])) {
            $sanitary_values['remove_from_wishlist_after_adding_to_cart_2'] = $input['remove_from_wishlist_after_adding_to_cart_2'];
        }

        return $sanitary_values;
    }

    public function better_wishlist_section_info()
    {

    }

    public function redirect_to_wishlist_page_0_callback()
    {
        printf(
            '<input type="checkbox" name="better_wishlist_option_name[redirect_to_wishlist_page_0]" id="redirect_to_wishlist_page_0" value="redirect_to_wishlist_page_0" %s> <label for="redirect_to_wishlist_page_0">Redirect to wishlist page after adding a product to wishlist</label>',
            (isset($this->better_wishlist_options['redirect_to_wishlist_page_0']) && $this->better_wishlist_options['redirect_to_wishlist_page_0'] === 'redirect_to_wishlist_page_0') ? 'checked' : ''
        );
    }

    public function redirect_to_cart_page_1_callback()
    {
        printf(
            '<input type="checkbox" name="better_wishlist_option_name[redirect_to_cart_page_1]" id="redirect_to_cart_page_1" value="redirect_to_cart_page_1" %s> <label for="redirect_to_cart_page_1">Redirect to cart page after adding a product to cart</label>',
            (isset($this->better_wishlist_options['redirect_to_cart_page_1']) && $this->better_wishlist_options['redirect_to_cart_page_1'] === 'redirect_to_cart_page_1') ? 'checked' : ''
        );
    }

    public function remove_from_wishlist_after_adding_to_cart_2_callback()
    {
        printf(
            '<input type="checkbox" name="better_wishlist_option_name[remove_from_wishlist_after_adding_to_cart_2]" id="remove_from_wishlist_after_adding_to_cart_2" value="remove_from_wishlist_after_adding_to_cart_2" %s> <label for="remove_from_wishlist_after_adding_to_cart_2">Remove from wishlist after adding to cart</label>',
            (isset($this->better_wishlist_options['remove_from_wishlist_after_adding_to_cart_2']) && $this->better_wishlist_options['remove_from_wishlist_after_adding_to_cart_2'] === 'remove_from_wishlist_after_adding_to_cart_2') ? 'checked' : ''
        );
    }

}

/*
 * Retrieve this value with:
 * $better_wishlist_options = get_option( 'better_wishlist_option_name' ); // Array of All Options
 * $redirect_to_wishlist_page_0 = $better_wishlist_options['redirect_to_wishlist_page_0']; // Redirect to wishlist page
 * $redirect_to_cart_page_1 = $better_wishlist_options['redirect_to_cart_page_1']; // Redirect to cart page
 * $remove_from_wishlist_after_adding_to_cart_2 = $better_wishlist_options['remove_from_wishlist_after_adding_to_cart_2']; // Remove from wishlist after adding to cart
 */
