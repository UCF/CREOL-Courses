<?php
/*
Template Name: Course Details Template
*/
get_header();
the_post(); 

$url = 'https://api.creol.ucf.edu/CoursesJson.asmx/Details?CourseID=' . $course_id;
$details_arr = get_json( $url );
if ( isset( $_GET['courseid'] ) ) {
	$post->post_title = $details_arr[0]->CourseName;
}
?>

<article class="<?php echo $post->post_status; ?> post-list-item">
	<?php
	if ( isset( $_GET['courseid'] ) ) {
		echo $details_arr[0]->Description . '<br>';
		echo $details_arr[0]->Details;
	} else {
		the_content();
	}
	?>
</article>

<?php get_footer(); ?>
