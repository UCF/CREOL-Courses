<?php
/**
 * 
 **/

function courses_list() {
	$undergrad_arr = get_json( 'https://api.creol.ucf.edu/CoursesJson.asmx/UndergradCourses' );
	$grad_arr = get_json( 'https://api.creol.ucf.edu/CoursesJson.asmx/GradCourses' );

	ob_start();
	?>
	<div style="padding: 5% 10% 5% 10%">
		<h2 id="undergrad" class="mb-0 auto-section" data-section-link-title="Undergraduate">Undergraduate Courses</h2>
		<hr class=" hr-2 hr-black my-2">
		<?php courses_list_display( UNDERGRAD ); ?>
	</div>
	<div style="padding: 0% 10% 5% 10%">
		<h2 id="grad" class="mb-0 auto-section" data-section-link-title="Graduate">Graduate Courses</h2>
		<hr class=" hr-2 hr-primary my-2">
		<?php courses_list_display( GRAD ); ?>
	</div>
	<?php
	return ob_get_clean();
}

function courses_list_display( $level ) {
	$url = ( $level == UNDERGRAD ) ? 'https://api.creol.ucf.edu/CoursesJson.asmx/UndergradCourses' : 'https://api.creol.ucf.edu/CoursesJson.asmx/GradCourses';
	$courses = get_json( $url );

	foreach ( $courses as $curr ) {
		?>
		<div class="px-2 pb-3">
			<span class="h-5 font-weight-bold letter-spacing-1">
				<?= $curr->Course . ' ' . $curr->Title ?>
			</span><br>
			<?php if ( $curr->Description != null ) : ?>
				<span>
					<?= $curr->Description ?>
				</span><br>
			<?php endif; ?>
			|
			<?= $curr->isDetail ? ( '<a href="details/?courseid=' . $curr->CourseID . '">Details</a> | ' ) : '' ?>
			<a href="course-schedule/?semester=0&instructor=-1&course=<?= $curr->CourseID ?>&level=<?= $level ?>">Schedule</a>
			|
			<a href="https://creol.ucf.edu/academics/undergrad/mission-educational-objectives-abet/abet-prep/">ABET</a>
			|
			<?= $curr->isSyllabus ? ( '<a href="syllabus/?scheduleid=' . $curr->CourseScheduleID . '&course=' . $curr->Course . '">Syllabus</a> | ' ) : '' ?>
		</div>
		<?php
	}
}