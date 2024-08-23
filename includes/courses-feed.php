<?php
/**
 * Handles JSON feed.
 * Takes in a URL and returns an object.
 **/
function get_json($url) {
    $transient = 'courses_' . md5($url);
    $items = get_transient($transient);
    $expiration = 60 * 60; // Cache expiration in seconds (1 hour).
    $args = array(
        'timeout' => 60,
    );

    if (!$items) {
        // Fetch the data from the API
        $request = wp_remote_get($url, $args);

        if (is_wp_error($request)) {
            error_log('WP Error: ' . $request->get_error_message());
            return null;
        }

        // Get and decode the body of the response
        $body = wp_remote_retrieve_body($request);
        $items = json_decode($body);

        // Log the raw body for debugging
        error_log('API Response Body: ' . $body);

        // Check for JSON errors
        if (json_last_error() !== JSON_ERROR_NONE) {
            error_log('JSON Decode Error: ' . json_last_error_msg());
            return null;
        }

        // Cache the result
        set_transient($transient, $items, $expiration);
    }

    // Return the cached or fetched items
    return $items;
}