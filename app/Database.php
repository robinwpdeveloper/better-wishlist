<?php

namespace BetterWishlist;

// If this file is called directly,  abort.
if (!defined('ABSPATH')) {
    die;
}

class Database
{
    public function __construct()
    {
        add_action('wp_login', [$this, 'update_db_and_cookie_when_login'], 10, 2);
    }

    public function update_db_and_cookie_when_login($user_login, WP_User $user)
    {
        global $wpdb;

        $session_id = sanitize_text_field($_COOKIE['better_wishlist_session_id']);

        if (!empty($session_id)) {
            $check_logged_user_wishlist = $wpdb->get_row("SELECT * FROM {$wpdb->better_wishlist_lists} WHERE user_id = {$user->ID}");
            $wishlist = $wpdb->get_row("SELECT ID FROM {$wpdb->better_wishlist_lists} WHERE session_id = '{$session_id}'");

            if (empty($check_logged_user_wishlist)) {
                $query = $wpdb->query("UPDATE {$wpdb->better_wishlist_lists} SET user_id = {$user->ID}, expiration = null, session_id = null WHERE session_id = '{$session_id}'");
                $query = $wpdb->query("UPDATE {$wpdb->better_wishlist_items} SET user_id = {$user->ID} WHERE wishlist_id = {$wishlist->ID}");
            } else {
                if ($wishlist->ID > 0) {
                    $query = $wpdb->query("UPDATE {$wpdb->better_wishlist_items} SET user_id = {$user->ID}, wishlist_id = {$check_logged_user_wishlist->ID} WHERE wishlist_id = {$wishlist->ID}");
                    $wpdb->query("DELETE FROM {$wpdb->better_wishlist_lists} WHERE session_id = '{$session_id}'");
                }
            }

            if ($query) {
                setcookie('better_wishlist_session_id', '', 1, "/");
            }
        }
    }
}
