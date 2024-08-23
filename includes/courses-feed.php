<?php
/**
 * Handles JSON feed.
 * Takes in a URL and returns an object.
 **/
function get_json( $url ) {
	$transient = 'courses_' . md5( $url );
	// $items = get_transient( $transient );
	$items = false;
	$expiration = 60; // Seconds in an hour.
	$args = array(
		'timeout' => 60,
	);

	if ( ! $items ) {
		error_log("Items DNE");
		$request = wp_remote_get( $url, $args );

		if ( is_wp_error( $request ) ) {
			echo 'Please email UCFTeam-CREOL-IT@groups.ucf.edu with the url, error message, and screenshot.\n';
			echo $request->get_error_message() . '\n';
			return false;
		}

		$items = json_decode( wp_remote_retrieve_body( $request ) );
		set_transient( $transient, $items, $expiration );
	}

	// $items = json_decode( wp_remote_retrieve_body( $request ) );

	$items = array( $items->response )[0];
	error_log("ITEMS EXISTS: " . $items);
	return $items;
}
