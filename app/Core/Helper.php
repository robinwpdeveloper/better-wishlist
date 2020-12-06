<?php

namespace BetterWishlist\Core;

// If this file is called directly, abort.
if (!defined('ABSPATH')) {
    die;
}

class Helper extends Singleton
{
    private function __construct()
    {

    }

    public function create_page($slug, $option = '', $page_title = '', $page_content = '', $post_parent = 0)
    {
        global $wpdb;

        $option_value = get_option($option);

        if ($option_value > 0) {
            $page_object = get_post($option_value);

            if ($page_object && 'page' === $page_object->post_type && !in_array($page_object->post_status,
                ['pending', 'trash', 'future', 'auto-draft'], true)) {
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
            $page_data = [
                'ID' => $page_id,
                'post_status' => 'publish',
            ];
            wp_update_post($page_data);
        } else {
            $page_data = [
                'post_status' => 'publish',
                'post_type' => 'page',
                'post_author' => 1,
                'post_name' => $slug,
                'post_title' => $page_title,
                'post_content' => $page_content,
                'post_parent' => $post_parent,
                'comment_status' => 'closed',
            ];
            $page_id = wp_insert_post($page_data);
        }

        if ($option) {
            update_option($option, $page_id);
        }

        return $page_id;
    }

}
