<?php
/*
Template Name: Course Details Template
*/
function details_page( $course_id ) {
	$url = 'https://api.creol.ucf.edu/CoursesJson.asmx/Details?CourseID=' . $course_id;
	$details_arr = get_json( $url );

	echo $details_arr[0]->CourseName . '<br>';
	echo $details_arr[0]->Description . '<br>';
	echo $details_arr[0]->Details;
}
get_header();
the_post(); ?>

<article class="<?php echo $post->post_status; ?> post-list-item">
	<?php
	if ( isset( $_GET['courseid'] ) ) {
		details_page( $_GET['courseid'] );
	} else {
		the_content();
	}
	?>
</article>

<?php get_footer(); ?>
