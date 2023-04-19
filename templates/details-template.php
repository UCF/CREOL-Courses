<?php
/*
Template Name: Course Details Template
*/
function details_page( $course_id, $post ) {
	$url = 'https://api.creol.ucf.edu/CoursesJson.asmx/Details?CourseID=' . $course_id;
	$details_arr = get_json( $url );
	$post->post_title = $details_arr[0]->CourseName;
?>
<div class="container m-5">
	<?= $post->post_title ?>
	<?= $details_arr[0]->Description ?>
	<br>
	<?= $details_arr[0]->Details ?>
</div>
<?php
}

get_header();
the_post(); ?>

<article class="<?php echo $post->post_status; ?> post-list-item">
	<?php
	if ( isset( $_GET['courseid'] ) ) {
		details_page( $_GET['courseid'], $post );
	} else {
		the_content();
	}
	?>
</article>

<?php get_footer(); ?>
