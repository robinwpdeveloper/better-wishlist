<?php

/**
 * Plugin Name:       Better Wishlist
 * Plugin URI:        #
 * Description:       Wishlist functionality for your WooCommerce store.
 * Version:           1.0.0
 * Author:            WPDeveloper
 * Author URI:        https://wpdeveloper.net/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       better-wishlist
 * Domain Path:       /languages
 *
 * @package           BetterWishlist
 */

// If this file is called directly, abort.
if (!defined('ABSPATH')) {
    die;
}

// Require composer autoloader
require plugin_dir_path(__FILE__) . 'vendor/autoload.php';

// Running the plugin.
\BetterWishlist\Core\Plugin::instance();

// Seed necessary things on DB.
register_activation_hook(__FILE__, array('Better_Wishlist_Install', 'install'));
