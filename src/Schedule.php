<?php

namespace BetterWishlist;

// If this file is called directly,  abort.
if (!defined('ABSPATH')) {
    die;
}

class Schedule
{
    public function __construct()
    {
        add_action('better_wishlist_delete_expired_wishlist', [$this, 'delete_expired_wishlist']);

        if (!wp_next_scheduled('better_wishlist_delete_expired_wishlist') && !wp_installing()) {
            wp_schedule_event(time(), 'twicedaily', 'better_wishlist_delete_expired_wishlist');
        }
    }

    public function delete_expired_wishlist()
    {
        global $wpdb;

        $count = $wpdb->get_var("SELECT count(ID) FROM {$wpdb->better_wishlist_lists} WHERE CURTIME() >= expire_on AND user_id IS NULL");

        if ($count > 0) {
            $wpdb->query("DELETE T1,T2 FROM {$wpdb->better_wishlist_lists} T1 INNER JOIN {$wpdb->better_wishlist_items} T2 on T1.ID = T2.wishlist_id WHERE CURTIME() >= expire_on AND T1.user_id IS NULL");
        }
    }

}
