<?php

add_action('wprs_build_settings', 'wprs_build_settings_callback');
function wprs_build_settings_callback($config)
{

    // build settings
    $config::add_tab([
        'title' => __('General Settings', 'better-wishlist'),
        'id' => 'general_settings',
    ]);

    $config::add_field('general_settings', [
        'id' => 'add_to_wishlist_text',
        'type' => 'text',
        'title' => __('Add to wishlist button text', 'better-wishlist'),
        'default' => 'Add to wishlist',
    ]);

    $config::add_field('general_settings', [
        'id' => 'added_to_wishlist_text',
        'type' => 'text',
        'title' => __('"Product added to Wishlist" Text', 'better-wishlist'),
        'default' => 'Added to Wishlist',
    ]);

    $config::add_field('general_settings', [
        'id' => 'already_in_wishlist',
        'type' => 'text',
        'title' => __('"Product already in Wishlist" Text', 'better-wishlist'),
        'default' => 'Already in Wishlist',
    ]);

    $config::add_field('general_settings', [
        'id' => 'browse_wishlist',
        'type' => 'text',
        'title' => __('"Browse Wishlist" Text', 'better-wishlist'),
        'default' => 'Browse Wishlist',
    ]);

    $config::add_field('general_settings', [
        'id' => 'wishlist_page_redirect',
        'type' => 'radio',
        'title' => __('Radio', 'rwprs'),
        'title' => __('Redirect to wishlist page', 'better-wishlist'),
        'desc' => __('Select whether redirect after adding to wishlist', 'better-wishlist'),
        'options' => array(
            true => 'Yes',
            false => 'No',
        ),
        'default' => false,
    ]);

    $config::add_field('general_settings', [
        'id' => 'cart_page_redirect',
        'type' => 'radio',
        'title' => __('Redirect to cart page', 'better-wishlist'),
        'desc' => __('Select whether redirect cart page after adding to cart from wishlist page', 'better-wishlist'),
        'options' => [
            true => 'Yes',
            false => 'No',
        ],
        'default' => false,
    ]);

    $config::add_field('general_settings', [
        'id' => 'remove_from_wishlist',
        'type' => 'radio',
        'title' => __('Remove From Wishlist', 'better-wishlist'),
        'desc' => __('Remove from wishlist after adding to cart', 'better-wishlist'),
        'options' => [
            true => 'Yes',
            false => 'No',
        ],
        'default' => false,
    ]);

}
