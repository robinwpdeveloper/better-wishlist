<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class Better_Wishlist_Helper {
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
            return apply_filters('better_wishlist_locate_template', $plugin_path, $path);
        }

        return apply_filters('better_wishlist_locate_template', $located, $path);
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

        return apply_filters('better_wishlist_page_id', $wishlist_page_id);
    }

    /**
     * Returns true if it finds that you're printing a single product
     * Should return false in any loop (including the ones inside single product page)
     *
     * @return bool Whether you're currently on single product template
     * @since 3.0.0
     */
    public static function wishlist_is_single () {
        return apply_filters('better_wishlist_is_single', is_product() && !in_array(wc_get_loop_prop('name'),
                array('related', 'up-sells')) && !wc_get_loop_prop('is_shortcode'));
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

    public static function update_db_and_cookie_in_login($user_login, WP_User $user)
    {
        global $wpdb;

        $session_id = sanitize_text_field($_COOKIE['better_wishlist_session_id']);

        if (!empty($session_id)) {

            $check_login_user_wishlist = $wpdb->get_row("SELECT * FROM {$wpdb->ea_wishlist_lists} WHERE user_id = {$user->ID}");


            $wishlist = $wpdb->get_row("SELECT ID FROM {$wpdb->ea_wishlist_lists} WHERE session_id = '{$session_id}'");

            if (empty($check_login_user_wishlist)) {
                $query = $wpdb->query("UPDATE {$wpdb->ea_wishlist_lists} SET user_id = {$user->ID}, expiration = null, session_id = null WHERE session_id = '{$session_id}'");

                $query = $wpdb->query("UPDATE {$wpdb->ea_wishlist_items} SET user_id = {$user->ID} WHERE wishlist_id = {$wishlist->ID}");
            } else {
                
                if ($wishlist->ID > 0) {

                    $query = $wpdb->query("UPDATE {$wpdb->ea_wishlist_items} SET user_id = {$user->ID}, wishlist_id = {$check_login_user_wishlist->ID} WHERE wishlist_id = {$wishlist->ID}");

                    $wpdb->query("DELETE FROM {$wpdb->ea_wishlist_lists} WHERE session_id = '{$session_id}'");
                }
            }

            if ($query) {
                setcookie('better_wishlist_session_id', '', 1, "/");
            }
        }
    }

    public static function delete_expired_wishlist()
    {
        global $wpdb;
        $count = $wpdb->get_var("SELECT count(ID) FROM {$wpdb->ea_wishlist_lists} WHERE CURTIME() >= expiration AND user_id IS NULL");
        
        if( $count > 0 ) {
            $wpdb->query("DELETE T1,T2 FROM {$wpdb->ea_wishlist_lists} T1 INNER JOIN {$wpdb->ea_wishlist_items} T2 on T1.ID = T2.wishlist_id WHERE CURTIME() >= expiration AND T1.user_id IS NULL");
        }
    }

}
