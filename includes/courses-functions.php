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

// Hour * 4 + intdiv( Minutes, 15 )
function get_row( $time ) {
	$time = strtotime( $time );
	$hour = idate( "H", $time );
	$min = idate( "i", $time );

	return ( $hour * 4 ) + intdiv( $min, 15 );
}

function matrix_timetable( $timetable_json ) {
	// $min_time = 32;		// 8:00 am
	// $max_time = 80;		// 8:00 pm
	// $col = 0;

	// $table = array( );

	// foreach ( $timetable_json as $course ) {
	// 	$start_row = get_row( $course->StartTime ) - $min_time;
	// 	$end_row = get_row( $course->EndTime ) - $min_time;

	// 	while ( isset( $table[$start_row][$col] ) ) {
	// 		// create another col and move there
	// 		$col += 1;
	// 	}

	// 	$table[$start_row][$col] = $course;
		
	// 	for ( $i = $start_row + 1; i <= $end_row; $i++ ) {
	// 		$table[$i][$col] = 1;
	// 	}
	// }

	// echo var_dump( $table );
}