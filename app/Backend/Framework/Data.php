<?php

namespace BetterWishlist\Backend\Framework;

// If this file is called directly, abort.
if (!defined('ABSPATH')) {
    die;
}

trait Data
{
    /**
     * Set default settings data in database
     *
     * @return null save option in database
     */
    public function save_default_settings()
    {
        $field = [];

        foreach (Builder::get_settings() as $setting_item) {
            if (!isset($setting_item['group'])) {
                //normal field
                if (isset($setting_item['fields'])) {
                    foreach ($setting_item['fields'] as $field_item) {
                        $field[$field_item['id']] = $field_item['default'];
                    }
                }
            } else {
                // group field
                foreach ($setting_item['group'] as $group_key => $group_item) {
                    $group = [];
                    if (isset($group_item['fields'])) {
                        foreach ($group_item['fields'] as $group_field) {
                            $group[][$group_field['id']] = (isset($group_field['default']) ? $group_field['default'] : '');
                        }
                    }
                    $field[$setting_item['id']][$group_key] = $group;
                }
            }
        }

        if (get_option($this->option_name) !== false) {
            $defaults = json_decode(get_option($this->option_name), true);
            $args = wp_parse_args($defaults, $field);

            update_option($this->option_name, json_encode($args));
        } else {
            add_option($this->option_name, json_encode($field));
        }
    }

    /**
     * Get settings.
     *
     * @since 1.0.0
     */
    public function get_setting($key)
    {
        $settings = json_decode(get_option($this->option_name), true);

        if (isset($settings->{$key})) {
            return $settings->{$key};
        }

        return;
    }

    /**
     * Get settings.
     *
     * @since 1.0.0
     */
    public function update_setting($args)
    {
        $settings = json_decode(get_option($this->option_name), true);

        if (isset($settings->{$key})) {
            $settings = wp_parse_args($args, $settings);

            return update_option($this->option_name, json_encode($settings));
        }

        return false;
    }
}
