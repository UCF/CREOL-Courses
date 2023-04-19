<?php
/*
Template Name: Course Details Template
*/
function details_page( $course_id ) {
	$url = 'https://api.creol.ucf.edu/CoursesJson.asmx/Details?CourseID=' . $course_id;
	$details_arr = get_json( $url );
?>
<div class="container">
	<h1 class="mt-3 mt-sm-4 mt-md-5 mb-3"><?= $details_arr[0]->CourseName ?></h1>
</div>
<div style="margin: 5% 10% 5% 10%">
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
		details_page( $_GET['courseid'] );
	} else {
		the_content();
	}
	?>
</article>

<?php get_footer(); ?>
