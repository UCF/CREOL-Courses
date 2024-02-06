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
		<div class="mt-3 mt-sm-4 mt-md-5 mb-3">
			<h1>
				<?= $details_arr[0]->Course . '- ' . $details_arr[0]->Title ?>
			</h1>
		</div>
		<div class="row">
			<div class="col">
				<div class="mt-4 mb-5 pb-sm-4">
					<?= $details_arr[0]->Details ?>
				</div>
			</div>
			<div class="col-2">
				<p class="font-weight-bold letter-spacing-1">Syllabi</p>
				<ul class="list-group list-group-flush">
					<?php foreach ( $syllabus_arr as $curr ) : ?>
						<li class="list-group-item">
							<a href="https://api.creol.ucf.edu/ViewSyllabus.aspx/?CourseScheduleID=<?= $curr->CourseScheduleID ?>"
								target="_blank"><?= $curr->Semester ?></a>
						</li>
					<?php endforeach; ?>
				</ul>
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
		?>
		<div class="container">
			Go to the <a href="/courses/">Courses page</a> for details on a specific course.
		</div>
		<?php
	}
	?>
</article>

<?php get_footer(); ?>