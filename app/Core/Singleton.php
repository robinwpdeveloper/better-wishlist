<?php

namespace BetterWishlist\Core;

// If this file is called directly, abort.
if (!defined('ABSPATH')) {
    die;
}

class Singleton
{
    private static $instance = null;

    public static function instance()
    {
        if (self::$instance === null) {
            self::$instance = new static();
        }

        return self::$instance;
    }
}
