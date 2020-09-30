<?php

// If this file is called directly, abort.
if (!defined('ABSPATH')) {
    die;
}

if (!class_exists('User_Wishlist')) {
    class User_Wishlist
    {
        protected static $instance;

        public static function get_instance()
        {
            if (is_null(self::$instance)) {
                self::$instance = new self();
            }

            return self::$instance;
        }

        public $user_id;

        private $token;

        private $default_name;

        private $default_privacy;

        private $session_id;

        private $is_default;

        private $slug;

        public function __construct()
        {
        }

        public function create()
        {
            global $wpdb;

            $columns = [
                'wishlist_privacy' => '%d',
                'wishlist_name' => '%s',
                'wishlist_slug' => '%s',
                'wishlist_token' => '%s',
                'is_default' => '%d'
            ];

            $values = [
                0,
                __('Wishlist', 'wishlist'),
                '',
                uniqid(),
                0
            ];

            if (!is_user_logged_in()) {
                $columns['session_id'] = '%s';
                $values[] = $this->generate_session_id();
            } else {
                $columns['user_id'] = '%d';
                $values[] = get_current_user_id();
            }

            $columns['dateadded'] = 'FROM_UNIXTIME( %d )';
            $values[] = current_time('timestamp');

            if (!is_user_logged_in()) {
                $columns['expiration'] = 'FROM_UNIXTIME( %d )';
                $values[] = current_time('timestamp');
            }

            $query_columns = implode(', ', array_map('esc_sql', array_keys($columns)));
            $query_values = implode(', ', array_values($columns));

            $query = "INSERT INTO {$wpdb->ea_wishlist_lists} ( {$query_columns} ) VALUES ( {$query_values} ) ";

            $res = $wpdb->query($wpdb->prepare($query, $values));



            if ($res) {
                $id = apply_filters('wishlist_successfully_created', intval($wpdb->insert_id));
            }

            
        }

        /**
         * Get variable for default share key
         *
         * @return string
         */
        function generate_wishlist_token()
        {
            return filter_input(INPUT_COOKIE, 'tinv_wishlistkey', FILTER_VALIDATE_REGEXP, array(
                'options' => array(
                    'regexp'  => '/^[A-Fa-f0-9]{6}$/',
                    'default' => $this->token,
                ),
            ));
        }

        public static function generate_session_id()
        {
            $session_id = '';

            if (is_user_logged_in()) {
                return false;
            }

            require_once ABSPATH . 'wp-includes/class-phpass.php';
            $hasher      = new PasswordHash(8, false);
            $session_id = md5($hasher->get_random_bytes(32));

            return $session_id;
        }
    }
}

function User_Wishlist()
{
    return User_Wishlist::get_instance();
}
