<?php
/*
Template Name: Course Details Template
*/
function details_page( $course_id ) {
	$details_url = 'https://api.creol.ucf.edu/CoursesJson.asmx/Details?CourseID=' . $course_id;
	$syllabus_url = 'https://api.creol.ucf.edu/CoursesJson.asmx/SyllabusList?CourseID=' . $course_id;
	$details_arr = get_json( $details_url );
	$syllabus_arr = get_json( $syllabus_url );
?>
<div class="container">
	<h1 class="mt-3 mt-sm-4 mt-md-5 mb-3"><?= $details_arr[0]->CourseName ?></h1>
	<div class="row">
		<div class="col">
			<?php foreach ( $syllabus_arr as $curr ) : ?>
				<a href="" target="_blank"><?= $curr->Semester ?></a><br>
			<?php endforeach; ?>
		</div>
		<div class="col">
			Description: <?= $details_arr[0]->Description ?>
			<div class="mt-4 mb-5 pb-sm-4">
				<?= $details_arr[0]->Details ?>
			</div>
		</div>
	</div>
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
