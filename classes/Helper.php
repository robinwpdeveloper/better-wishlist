<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class Helper {
    /**
     * The function overwrites the method output templates woocommerce
     *
     * @param  string  $template_name  Name file template.
     * @param  array   $args           Array variable in template.
     * @param  string  $template_path  Customization path.
     */
    public static function better_wishlist_locate_template ($path, $var = null) {
        $woocommerce_base = WC()->template_path();
        $template_woocommerce_path = $woocommerce_base.$path;
        $template_path = '/'.$path;
        $plugin_path = BETTER_WISHLIST_PLUGIN_PATH.'templates/'.$path;

        $located = locate_template(
            array(
                $template_woocommerce_path,
                $template_path
            )
        );

        if (!$located && file_exists($plugin_path)) {
            return apply_filters('wishlist_locate_template', $plugin_path, $path);
        }

        return apply_filters('wishlist_locate_template', $located, $path);
    }

    public static function better_wishlist_get_template ($path, $var = null, $return = false) {
        $located = self::better_wishlist_locate_template($path, $var);

        if ($var && is_array($var)) {
            $atts = $var;
            extract($var);
        }

        if ($return) {
            ob_start();
        }

        include($located);

        if ($return) {
            return ob_get_clean();
        }
    }

    /**
     * Retrive wishlist page id, if any
     *
     * @return int Wishlist page id
     */
    public static function get_wishlist_page_id () {
        $wishlist_page_id = get_option('better_wishlist_page_id');

        return apply_filters('wishlist_page_id', $wishlist_page_id);
    }

    /**
     * Returns true if it finds that you're printing a single product
     * Should return false in any loop (including the ones inside single product page)
     *
     * @return bool Whether you're currently on single product template
     * @since 3.0.0
     */
    public static function wishlist_is_single () {
        return apply_filters('wishlist_is_single', is_product() && !in_array(wc_get_loop_prop('name'),
                array('related', 'up-sells')) && !wc_get_loop_prop('is_shortcode'));
    }

    public static function format_fragment_options ($options, $context = '') {
        // removes unusable values, and changes options common for all fragments
        if (!empty($options)) {
            foreach ($options as $id => $value) {
                if (is_object($value) || is_array($value)) {
                    // remove item if type is not supported
                    unset($options[$id]);
                } elseif ('ajax_loading' == $id) {
                    $options['ajax_loading'] = false;
                }
            }
        }

        // applies context specific changes
        if (!empty($context)) {
            $options['item'] = $context;

            switch ($context) {
                case 'add_to_wishlist':
                    unset($options['template_part']);
                    unset($options['label']);
                    unset($options['exists']);
                    unset($options['icon']);
                    unset($options['link_classes']);
                    unset($options['link_popup_classes']);
                    unset($options['container_classes']);
                    unset($options['found_in_list']);
                    unset($options['found_item']);
                    unset($options['popup_title']);
                    unset($options['wishlist_url']);
                    break;
            }
        }

        return $options;
    }

    public static function better_wishlist_create_page ($slug, $option = '', $page_title = '', $page_content = '', $post_parent = 0) {
        global $wpdb;

        $option_value = get_option($option);

        if ($option_value > 0) {
            $page_object = get_post($option_value);

            if ($page_object && 'page' === $page_object->post_type && !in_array($page_object->post_status,
                    array('pending', 'trash', 'future', 'auto-draft'), true)) {
                return $page_object->ID;
            }
        }
        $page_found = self::page_already_found($slug, $page_content);

        if ($page_found) {

            if ($option) {
                update_option($option, $page_found);
            }
            return $page_found;
        }
        $trash_found = self::page_already_found($slug, $page_content, true);

        if ($trash_found) {
            $page_id = $trash_found;
            $page_data = array(
                'ID'          => $page_id,
                'post_status' => 'publish',
            );
            wp_update_post($page_data);
        } else {
            $page_data = array(
                'post_status'    => 'publish',
                'post_type'      => 'page',
                'post_author'    => 1,
                'post_name'      => $slug,
                'post_title'     => $page_title,
                'post_content'   => $page_content,
                'post_parent'    => $post_parent,
                'comment_status' => 'closed',
            );
            $page_id = wp_insert_post($page_data);
        }

        if ($option) {
            update_option($option, $page_id);
        }

        return $page_id;
    }

    private static function page_already_found ($slug, $content, $check_trash = false) {
        global $wpdb;
        $check_status = "post_status NOT IN ( 'pending', 'trash', 'future', 'auto-draft' )";
        if ($check_trash) {
            $check_status = "post_status = 'trash'";
        }
        if (strlen($content) > 0) {
            if (!$check_trash) {
                $content = str_replace(array('<!-- wp:shortcode -->', '<!-- /wp:shortcode -->'), '', $content);
            }
            $found = $wpdb->get_var($wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE post_type='page' AND {$check_status} AND post_content LIKE %s LIMIT 1;",
                "%{$content}%"));
        } else {
            $found = $wpdb->get_var($wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE post_type='page' AND {$check_status}  AND post_name = %s LIMIT 1;",
                $slug));
        }
        return $found;
    }
}
