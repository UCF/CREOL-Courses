<?php
/**
 * Helper functions for the course info.
 **/

// Returns current semester serial.
function semester_serial() {
	if ( ( ( date( 'm', strtotime(' + 2 months') ) - 1 ) / 4 ) == 1 ){
		return ( date( 'Y', strtotime(' + 2 months') ) - 1980 ) * 3;
	} else {
		return ( date( 'Y', strtotime(' + 2 months') ) - 1980 ) * 3 + intdiv( ( date( 'm', strtotime(' + 2 months') ) - 1 ), 4 );
	}
}

// Formats the class days.
function class_days( $mon, $tue, $wed, $thu, $fri ) {
	if ( $mon || $tue || $wed || $thu || $fri ) {
		return ( $mon ? 'Mondays, ' : '' ) . ( $tue ? 'Tuesdays, ' : '' ) . ( $wed ? 'Wednesdays, ' : '' ) . ( $thu ? 'Thursdays, ' : '' ) . ( $fri ? 'Fridays, ' : '' );
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