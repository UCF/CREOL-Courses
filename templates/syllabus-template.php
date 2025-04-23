<?php
/*
Template Name: Course Syllabus Template
Grabs info and styles the syllabus page
*/
function syllabus_pdf( $schedule_id, $course ) {
	$url = 'https://api.creol.ucf.edu/ViewSyllabus.aspx?CourseScheduleID=' . $schedule_id;
	$content = wp_remote_get( $url );
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
} else {
	get_header();
	?>
	<div class="container">
		Go to the <a href="/courses/">Courses page</a> to get a course syllabus.
	</div>
	<?php
	get_footer();
}
