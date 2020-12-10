<?php

namespace BetterWishlist;

// If this file is called directly,  abort.
if (!defined('ABSPATH')) {
    die;
}

class Model
{
    private $user_id;
    private $session_id;
    private $better_wishlist_lists;
    private $better_wishlist_items;

    public function __construct()
    {
        global $wpdb;

        $this->user_id = get_current_user_id();
        $this->session_id = $this->generate_session_id();
        $this->better_wishlist_lists = $wpdb->prefix . 'better_wishlist_lists';
        $this->better_wishlist_items = $wpdb->prefix . 'better_wishlist_items';

        add_action('wp_login', [$this, 'update_db_and_cookie_on_login'], 10, 2);
    }

    public function generate_session_id()
    {
        if ($this->user_id) {
            return false;
        }

        if (isset($_COOKIE['better_wishlist_session_id']) && !empty($_COOKIE['better_wishlist_session_id'])) {
            return $_COOKIE['better_wishlist_session_id'];
        }

        return md5(rand());
    }

    public function update_db_and_cookie_on_login($user_login, $user)
    {
        global $wpdb;

        if (isset($_COOKIE['better_wishlist_session_id'])) {
            $session_id = sanitize_text_field($_COOKIE['better_wishlist_session_id']);
            $check_logged_user_wishlist = $wpdb->get_row("SELECT * FROM {$this->better_wishlist_lists} WHERE user_id = {$user->ID}");
            $wishlist = $wpdb->get_row("SELECT ID FROM {$this->better_wishlist_lists} WHERE session_id = '{$session_id}'");

            if (empty($check_logged_user_wishlist)) {
                $query = $wpdb->query("UPDATE {$this->better_wishlist_lists} SET user_id = {$user->ID}, expiration = null, session_id = null WHERE session_id = '{$session_id}'");
                $query = $wpdb->query("UPDATE {$this->better_wishlist_items} SET user_id = {$user->ID} WHERE wishlist_id = {$wishlist->ID}");
            } else {
                if ($wishlist->ID > 0) {
                    $query = $wpdb->query("UPDATE {$this->better_wishlist_items} SET user_id = {$user->ID}, wishlist_id = {$check_logged_user_wishlist->ID} WHERE wishlist_id = {$wishlist->ID}");
                    $wpdb->query("DELETE FROM {$this->better_wishlist_lists} WHERE session_id = '{$session_id}'");
                }
            }

            if ($query) {
                setcookie('better_wishlist_session_id', '', 1, "/");
            }
        }
    }

    public function create_list()
    {
        global $wpdb;

        $columns = [
            'wishlist_privacy' => '%d',
            'wishlist_name' => '%s',
            'wishlist_slug' => '%s',
            'wishlist_token' => '%s',
            'is_default' => '%d',
        ];

        $values = [
            0,
            __('Wishlist', 'better-wishlist'),
            '',
            uniqid(),
            0,
        ];

        if (!is_user_logged_in()) {
            $columns['session_id'] = '%s';
            $values[] = $this->session_id;

            setcookie('better_wishlist_session_id', $this->session_id, time() + 86400, "/");
        } else {
            $columns['user_id'] = '%d';
            $values[] = $this->user_id;
        }

        $columns['dateadded'] = 'FROM_UNIXTIME( %d )';
        $values[] = current_time('timestamp');

        if (!is_user_logged_in()) {
            $columns['expiration'] = 'FROM_UNIXTIME( %d )';
            $timestamp = strtotime('+1 day', current_time('timestamp'));

            $values[] = $timestamp;
        }

        $query_columns = implode(', ', array_map('esc_sql', array_keys($columns)));
        $query_values = implode(', ', array_values($columns));

        $query = "INSERT INTO {$this->better_wishlist_lists} ( {$query_columns} ) VALUES ( {$query_values} ) ";

        $res = $wpdb->query($wpdb->prepare($query, $values));

        if ($res) {
            return apply_filters('better_wishlist_successfully_created', intval($wpdb->insert_id));
        }

        return false;
    }

    public function read_list($wishlist_id)
    {
        if (empty($wishlist_id)) {
            return;
        }

        global $wpdb;
        $wishlist_id = sanitize_text_field($wishlist_id);
        $query = "SELECT DISTINCT product_id,user_id,wishlist_id FROM {$this->better_wishlist_items} WHERE wishlist_id = {$wishlist_id}";

        $res = $wpdb->get_results($query, OBJECT);

        return $res;
    }

    public function get_current_user_list()
    {
        global $wpdb;

        $wishlist_id = null;

        if (is_user_logged_in()) {
            $wishlist_id = $wpdb->get_var("SELECT ID FROM {$this->better_wishlist_lists} WHERE user_id = {$this->user_id}");
        } else {
            $wishlist_id = $wpdb->get_var("SELECT ID FROM {$this->better_wishlist_lists} WHERE session_id = '{$this->session_id}'");
        }

        if ($wishlist_id) {
            return $wishlist_id;
        }

        return false;
    }

    public function item_in_list($product_id, $wishlist_id)
    {
        global $wpdb;

        if (empty($product_id)) {
            return false;
        }

        if (empty($product_id)) {
            return false;
        }

        $product_id = sanitize_text_field($product_id);
        $wishlist_id = sanitize_text_field($wishlist_id);
        $result = $wpdb->get_row("SELECT * FROM {$this->better_wishlist_items} WHERE wishlist_id = '{$wishlist_id}' and product_id = {$product_id}");

        return !empty($result);
    }

    public function insert_item($product_id, $wishlist_id)
    {
        global $wpdb;

        if (empty($product_id) || empty($wishlist_id)) {
            return false;
        }

        error_log($wishlist_id);

        $columns = [
            'product_id' => '%d',
            'quantity' => '%d',
            'wishlist_id' => '%d',
            'stock_status' => '%s',
            'original_price' => '%d',
            'original_currency' => '%s',
            'on_sale' => '%s',
            'user_id' => '%d',
        ];

        $product = wc_get_product($product_id);

        $values = [
            $product_id,
            1,
            $wishlist_id,
            $product->get_stock_status(),
            Plugin::instance()->helper->get_product_price($product),
            get_woocommerce_currency(),
            $product->is_on_sale(),
            get_current_user_id(),
        ];

        $columns['dateadded'] = 'FROM_UNIXTIME( %d )';
        $values[] = current_time('timestamp');

        $query_columns = implode(', ', array_map('esc_sql', array_keys($columns)));
        $query_values = implode(', ', array_values($columns));

        $query = "INSERT INTO {$this->better_wishlist_items} ( {$query_columns} ) VALUES ( {$query_values} ) ";

        $res = $wpdb->query($wpdb->prepare($query, $values));

        if ($res) {
            return apply_filters('better_wishlist_item_added_successfully', $wpdb->insert_id);
        }

        return false;
    }

    public function delete_item($product_id, $json = true)
    {

        if (empty($product_id)) {
            return false;
        }

        global $wpdb;

        $res = $wpdb->delete($this->better_wishlist_items, ['product_id' => sanitize_text_field($product_id)], ['%d']);
        if (!$json) {
            return $res;
        }

        if ($res) {
            wp_send_json_success();
        }
        wp_send_json_error();

    }
}
