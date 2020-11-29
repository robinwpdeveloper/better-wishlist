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
    // $config::add_field('general_settings', [
    //     'id' => 'wishlist_page_redirect',
    //     'type' => 'checkbox',
    //     'title' => __('Redirect to wishlist page', 'better-wishlist'),
    //     'desc' => __('Select whether redirect after adding to wishlist', 'better-wishlist'),
    //     'default' => false, // 1 = on | 0 = off
    // ]);

    // $config::add_field('general_settings', [
    //   'id' => 'wishlist_page_redirect',
    //   'type' => 'select',
    //   'title' => __('Redirect to wishlist page', 'better-wishlist'),
    //   'desc' => __('Select whether redirect after adding to wishlist', 'better-wishlist'),
    //   'default' => false,
    //   'options' => [
    //       true => 'Yes',
    //       false => 'No',
    //   ],
    // ]);

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
    // $config::add_field('general_settings', [
    //   'id' => 'cart_page_redirect',
    //   'type' => 'checkbox',
    //   'title' => __('Redirect to cart page', 'better-wishlist'),
    //   'desc' => __('Select whether redirect cart page after adding to cart from wishlist page', 'better-wishlist'),
    //   'default' => false, // 1 = on | 0 = off
    // ]);

    // $config::add_field('general_settings', [
    //   'id' => 'remove_from_wishlist',
    //   'type' => 'checkbox',
    //   'title' => __('Remove From Wishlist', 'better-wishlist'),
    //   'desc' => __('Remove from wishlist after adding to cart', 'better-wishlist'),
    //   'default' => false, // 1 = on | 0 = off
    // ]);

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

    // $config::add_tab([
    //   'title' => __('Pro Settings', 'better-wishlist'),
    //   'id' => 'pro_settings',
    // ]);

    // $config::add_field('general_settings', [
    //     'id' => 'radiofield',
    //     'type' => 'radio',
    //     'title' => __('Radio', 'rwprs'),
    //     'subtitle' => __('Field Sub Title', 'wp-react-settings'),
    //     'desc' => __('Field Description', 'wp-react-settings'),
    //     'options' => array(
    //         '1' => 'Opt 1',
    //         '2' => 'Opt 2',
    //         '3' => 'Opt 3',
    //     ),
    //     'default' => '2',
    // ]);
    // $config::add_field('general_settings', [
    //     'id' => 'selectfield',
    //     'type' => 'select',
    //     'title' => __('Select', 'wp-react-settings'),
    //     'subtitle' => __('Field Sub Title', 'wp-react-settings'),
    //     'desc' => __('Field Description', 'wp-react-settings'),
    //     'default' => 'yellow',
    //     'options' => [
    //         'red' => 'red color',
    //         'yellow' => 'Yellow Color',
    //         'blue' => 'Blue Color',
    //     ],
    // ]);
    // // collapsible field
    // $config::add_field('general_settings', [
    //     'id' => 'collapible_field',
    //     'type' => 'collapsible',
    //     'title' => __('Collapsible Field Example', 'wprs'),
    //     'default' => false,
    // ]);
    // $config::add_field('general_settings', [
    //     'id' => 'adminbar_sp_list_structure_template',
    //     'type' => 'text',
    //     'title' => __('Item template:', 'wprs'),
    //     'default'   => '<strong>%TITLE%</strong> / %AUTHOR% / %DATE%',
    //     'condition' => [
    //         'collapible_field' => true,
    //     ],
    // ]);
    // $config::add_field('general_settings', [
    //     'id' => 'adminbar_sp_list_structure_title_length',
    //     'type' => 'text',
    //     'title' => __('Title length:', 'wprs'),
    //     'default'   => '45',
    //     'condition' => [
    //         'collapible_field' => true,
    //     ],
    // ]);
    // $config::add_field('general_settings', [
    //     'id' => 'adminbar_sp_list_structure_date_format',
    //     'type' => 'text',
    //     'title' => __('Date format:', 'wprs'),
    //     'default'   => 'M-d h:i:a',
    //     'desc'   => __('For item template use %TITLE% for post title, %AUTHOR% for post author and %DATE% for post scheduled date-time. You can use HTML tags with styles also', 'wprs'),
    //     'condition' => [
    //         'collapible_field' => true,
    //     ]
    // ]);
}
