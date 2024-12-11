<?php
/**
 * Template Name: City Table
 */

get_header();

// Add custom action hook before the table
do_action('before_city_table');
?>

<div class="city-search-container">
    <input type="text" id="city-search" placeholder="Search for a city...">
</div>

<table id="city-table">
    <thead>
        <tr>
            <th>Country</th>
            <th>City</th>
            <th>Temperature</th>
        </tr>
    </thead>
    <tbody>
        <?php
        global $wpdb;

        // Query to fetch cities, countries, and metadata
        $query = "
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
                AND tt.taxonomy = 'countries'
            ORDER BY 
                city_name ASC
        ";

        $results = $wpdb->get_results($query);

        if (!empty($results)) {
            foreach ($results as $row) {
                $latitude = $row->latitude;
                $longitude = $row->longitude;

                // Fetch temperature from OpenWeatherMap API
                $api_key = '1ed6a6a4440e2278f1b080fb873dabbb';
                $temperature = 'N/A'; // Default value

                if (!empty($latitude) && !empty($longitude)) {
                    $api_url = "https://api.openweathermap.org/data/2.5/weather?lat={$latitude}&lon={$longitude}&units=metric&appid={$api_key}";

                    $response = wp_remote_get($api_url);

                    if (is_wp_error($response)) {
                        error_log('OpenWeatherMap API request error: ' . $response->get_error_message());
                    } else {
                        $body = json_decode(wp_remote_retrieve_body($response), true);

                        if (isset($body['main']['temp'])) {
                            $temperature = $body['main']['temp'] . '°C';
                        } else {
                            error_log('OpenWeatherMap API response missing temperature: ' . print_r($body, true));
                        }
                    }
                } else {
                    error_log('Missing latitude or longitude for city.');
                }

                // Display only country, city, and temperature
                echo "<tr>
                        <td>" . esc_html($row->country_name) . "</td>
                        <td>" . esc_html($row->city_name) . "</td>
                        <td>" . esc_html($temperature) . "</td>
                      </tr>";
            }
        } else {
            echo '<tr><td colspan="3">No data found</td></tr>';
        }
        ?>
    </tbody>
</table>

<?php
// Add custom action hook after the table
do_action('after_city_table');

get_footer();
?>
