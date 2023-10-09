<?php
/**
 * Handles JSON feed.
 * Takes in a URL and returns an object.
 **/
function get_json( $url ) {
	// $transient = 'courses_' . md5( $url );
	// $items = get_transient( $transient );
	// $expiration = 60; // Seconds in an hour.

	// if ( ! $items ) {
	// 	$request = wp_remote_get( $url );

	// 	if ( is_wp_error( $request ) ) {
	// 		return false;
	// 	}

	// 	$items = json_decode( wp_remote_retrieve_body( $request ) );
	// 	set_transient( $transient, $items, $expiration );
	// }

	$request = wp_remote_get( $url );
	$items = json_decode( wp_remote_retrieve_body( $request ) );

	$items = array( $items->response )[0];

	return $items;
}