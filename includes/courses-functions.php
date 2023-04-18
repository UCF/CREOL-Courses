<?php
/**
 * Helper functions for the course info.
 **/

// Returns current semester serial.
function semester_serial() {
	return ( date( 'Y' ) - 1980 ) * 3 + intdiv( ( date( 'm' ) - 1 ), 4 );
}

// Formats the class days.
function class_days( $mon, $tue, $wed, $thu, $fri ) {
	if ( $mon || $tue || $wed || $thu || $fri ) {
		return ( $mon ? 'Mondays, ' : '' ) . ( $tue ? 'Tuesdays, ' : '' ) . ( $wed ? 'Wednesdays, ' : '' ) . ( $thu ? 'Thursdays, ' : '' ) . ( $fri ? 'Fridays, ' : '' );
	}
}

// Adds "CREOL" to room number.
function location( $room ) {
	if ( $room ) {
		return ( str_starts_with( $room, 'A' ) || is_numeric( $room ) ) ? ( 'CREOL ' . $room ) : $room;
	}
}

// Returns the instructor's URL by taking in their first and last name.
function instructor_url( $name ) {
	if ( $name ) {
		$find = [ '.', ' ', '"' ];
		$replace = [ '', '-', '' ];
		return 'https://creol.ucf.edu/person/' . str_replace( $find, $replace, $name );
	}
}
