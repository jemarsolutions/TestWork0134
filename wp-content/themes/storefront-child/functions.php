<?php
// Enqueue The Parent and Child Theme Styles
function storefront_child_enqueue_styles() {
    wp_enqueue_style('parent-style', get_template_directory_uri() . '/style.css');
    wp_enqueue_style('child-style', get_stylesheet_directory_uri() . '/style.css', array('parent-style'));
}
add_action('wp_enqueue_scripts', 'storefront_child_enqueue_styles');

// Register Thw Custom Post Type "Cities"
function create_cities_post_type() {
    $args = array(
        'labels' => array(
            'name' => __('Cities'),
            'singular_name' => __('City')
        ),
        'public' => true,
        'has_archive' => true,
        'supports' => array('title', 'editor', 'thumbnail'),
        'rewrite' => array('slug' => 'cities'),
    );
    register_post_type('cities', $args);
}
add_action('init', 'create_cities_post_type');

// Add Meta Box for Latitude and Longitude
function add_city_meta_boxes() {
    add_meta_box('city_coordinates', 'City Coordinates', 'city_coordinates_callback', 'cities', 'normal', 'high');
}
add_action('add_meta_boxes', 'add_city_meta_boxes');

function city_coordinates_callback($post) {
    $latitude = get_post_meta($post->ID, 'latitude', true);
    $longitude = get_post_meta($post->ID, 'longitude', true);
    echo '<div class="city_coordinates_container">';
    echo '<label for="latitude">Latitude:</label> <input type="text" name="latitude" id="latitude" value="' . esc_attr($latitude) . '" /><br />';
    echo '<label for="longitude">Longitude:</label> <input type="text" name="longitude" id="longitude" value="' . esc_attr($longitude) . '" />';
    echo '</div>';
}

function save_city_meta($post_id) {
    if (isset($_POST['latitude'])) {
        update_post_meta($post_id, 'latitude', sanitize_text_field($_POST['latitude']));
    }
    if (isset($_POST['longitude'])) {
        update_post_meta($post_id, 'longitude', sanitize_text_field($_POST['longitude']));
    }
}
add_action('save_post', 'save_city_meta');

// Register Custom Taxonomy "Countries"
function create_countries_taxonomy() {
    $args = array(
        'labels' => array(
            'name' => __('Countries'),
            'singular_name' => __('Country')
        ),
        'hierarchical' => true,
        'rewrite' => array('slug' => 'countries'),
    );
    register_taxonomy('countries', 'cities', $args);
}
add_action('init', 'create_countries_taxonomy');

// Create Widget for City and Temperature
define('OPENWEATHERMAP_API_KEY', '1ed6a6a4440e2278f1b080fb873dabbb');

class City_Temperature_Widget extends WP_Widget {
    public function __construct() {
        parent::__construct('city_temperature_widget', 'City Temperature Widget');
    }

    public function widget($args, $instance) {
        $city = get_posts(array('post_type' => 'cities', 'numberposts' => 1));
        if (!empty($city)) {
            $latitude = get_post_meta($city[0]->ID, 'latitude', true);
            $longitude = get_post_meta($city[0]->ID, 'longitude', true);
            $temperature = $this->get_temperature($latitude, $longitude);

            echo $args['before_widget'];
            echo '<h3>' . $city[0]->post_title . '</h3>';
            echo '<p>Temperature: ' . esc_html($temperature) . '&deg;C</p>';
            echo $args['after_widget'];
        }
    }

    private function get_temperature($latitude, $longitude) {
        $api_url = "https://api.openweathermap.org/data/2.5/weather?lat=$latitude&lon=$longitude&appid=" . OPENWEATHERMAP_API_KEY . "&units=metric";
        $response = wp_remote_get($api_url);
        if (is_wp_error($response)) {
            return 'N/A';
        }
        $data = json_decode(wp_remote_retrieve_body($response), true);
        return $data['main']['temp'] ?? 'N/A';
    }
}
function register_city_temperature_widget() {
    register_widget('City_Temperature_Widget');
}
add_action('widgets_init', 'register_city_temperature_widget');

// Custom Template with Table and AJAX Search
function enqueue_city_search_scripts() {
    wp_enqueue_script('city-search', get_stylesheet_directory_uri() . '/js/city-search.js', array('jquery'), null, true);
    wp_localize_script('city-search', 'ajax_object', array('ajax_url' => admin_url('admin-ajax.php')));
}
add_action('wp_enqueue_scripts', 'enqueue_city_search_scripts');

function ajax_city_search() {
    global $wpdb;

    $search_query = sanitize_text_field($_POST['query']);
    $api_key = '1ed6a6a4440e2278f1b080fb873dabbb'; // OpenWeatherMap API Key

    // Query to fetch cities, metadata, and taxonomy
    $query = $wpdb->prepare("
        SELECT 
            p.ID AS city_id,
            p.post_title AS city_name,
            tm.name AS country_name,
            pm_lat.meta_value AS latitude,
            pm_lon.meta_value AS longitude
        FROM 
            {$wpdb->prefix}posts p
        LEFT JOIN 
            {$wpdb->prefix}term_relationships tr ON (p.ID = tr.object_id)
        LEFT JOIN 
            {$wpdb->prefix}term_taxonomy tt ON (tr.term_taxonomy_id = tt.term_taxonomy_id)
        LEFT JOIN 
            {$wpdb->prefix}terms tm ON (tt.term_id = tm.term_id)
        LEFT JOIN 
            {$wpdb->prefix}postmeta pm_lat ON (p.ID = pm_lat.post_id AND pm_lat.meta_key = 'latitude')
        LEFT JOIN 
            {$wpdb->prefix}postmeta pm_lon ON (p.ID = pm_lon.post_id AND pm_lon.meta_key = 'longitude')
        WHERE 
            p.post_type = 'cities'
            AND p.post_status = 'publish'
            AND p.post_title LIKE %s
        ORDER BY 
            p.post_title ASC
    ", '%' . $wpdb->esc_like($search_query) . '%');

    $results = $wpdb->get_results($query);

    $formatted_results = array();

    foreach ($results as $row) {
        $latitude = $row->latitude;
        $longitude = $row->longitude;
        $temperature = 'N/A'; // Default value

        // Fetch temperature from OpenWeatherMap API
        if (!empty($latitude) && !empty($longitude)) {
            $api_url = "https://api.openweathermap.org/data/2.5/weather?lat={$latitude}&lon={$longitude}&units=metric&appid={$api_key}";
            $response = wp_remote_get($api_url);

            if (!is_wp_error($response)) {
                $body = json_decode(wp_remote_retrieve_body($response), true);
                if (isset($body['main']['temp'])) {
                    $temperature = $body['main']['temp'] . '°C';
                }
            }
        }

        $formatted_results[] = array(
            'city_name' => $row->city_name ?: 'N/A',
            'country_name' => $row->country_name ?: 'N/A',
            'latitude' => $latitude ?: 'N/A',
            'longitude' => $longitude ?: 'N/A',
            'temperature' => $temperature
        );
    }

    wp_send_json($formatted_results);
}
add_action('wp_ajax_city_search', 'ajax_city_search');
add_action('wp_ajax_nopriv_city_search', 'ajax_city_search');
