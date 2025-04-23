<?php
/**
 * Helper functions for the course info.
 * 
 * Calculates a unique serial number for the current academic semester.
 *
 * The system divides the year into 3 semesters:
 *   - Spring: January–April
 *   - Summer: May–August
 *   - Fall: September–December
 *
 * The function looks 2 months ahead to determine the active or upcoming semester,
 * then calculates the serial number starting from the base year 1980.
 *
 * Each year contributes 3 semesters, so:
 *   Serial = (Year - 1980) * 3 + SemesterIndex
 *   (SemesterIndex: 0 = Spring, 1 = Summer, 2 = Fall)
 *
 */

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