<?php
/*
Template Name: Course Syllabus Template
*/
function syllabus_pdf( $schedule_id, $course ) {
	$url = 'https://www2.qa.creol.ucf.edu/Academics/Courses/ViewSyllabus.aspx?CourseScheduleID=' . $schedule_id;
	$content = wp_remote_get( $url, array( 'sslverify' => false ) );
	if ( is_wp_error( $content ) ) {
		echo 'Error';
		return false;
	}

	header( 'Content-Type: application/pdf' );
	header( 'Content-disposition: attachment; filename=' . $course . '-syllabus.pdf' );
	echo wp_remote_retrieve_body( $content );
}

if ( isset( $_GET['scheduleid'] ) && isset( $_GET['course'] ) ) {
	syllabus_pdf( $_GET['scheduleid'], $_GET['course'] );
}