<?php
/**
 * 
 **/

function courses_list() {
	ob_start();
	?>
	<div style="padding: 5% 10% 5% 10%">
		<h2 id="undergrad" class="mb-0 auto-section" data-section-link-title="Undergraduate">Undergraduate Courses</h2>
		<hr class=" hr-2 hr-black my-2">
		<?php courses_list_display( UNDERGRAD, 0 ); ?>
	</div>
	<div style="padding: 0% 10% 5% 10%">
		<h2 id="grad-core" class="mb-0 auto-section" data-section-link-title="Core Graduate">Core Graduate Courses</h2>
		<hr class=" hr-2 hr-primary my-2">
		<?php courses_list_display( GRAD, 1 ); ?>
	</div>
	<div style="padding: 0% 10% 5% 10%">
		<h2 id="grad-electives" class="mb-0 auto-section" data-section-link-title="Graduate Electives">Graduate Electives</h2>
		<hr class=" hr-2 hr-primary my-2">
		<?php courses_list_display( GRAD, 0 ); ?>
	</div>
	<?php
	return ob_get_clean();
}

function courses_list_display( $level, $core ) {
	if ( $level == GRAD && $core = 1 ) {
		$url = 'https://api.creol.ucf.edu/CoursesJson.asmx/CoreGradCourses';
	} elseif ( $level == GRAD && $core = 0 ) {
		$url = 'https://api.creol.ucf.edu/CoursesJson.asmx/NonCoreGradCourses';
	} else {
		$url = 'https://api.creol.ucf.edu/CoursesJson.asmx/UndergradCourses';
	}
	// $url = ( $level == UNDERGRAD ) ? 'https://api.creol.ucf.edu/CoursesJson.asmx/UndergradCourses' : 'https://api.creol.ucf.edu/CoursesJson.asmx/GradCourses';
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
			<a
				href="course-schedule/?semester=<?= ALL_SEMESTERS ?>&instructor=<?= ALL_INSTRUCTORS ?>&course=<?= $curr->CourseID ?>&level=<?= $level ?>">Schedule</a>
			|
			<?= $curr->IsABET ? ( '<a href="https://creol.ucf.edu/academics/undergrad/mission-educational-objectives-abet/abet-prep/">ABET</a> | ' ) : '' ?>
			<?= $curr->isSyllabus ? ( '<a href="syllabus/?scheduleid=' . $curr->CourseScheduleID . '&course=' . $curr->Course . '">Syllabus</a> | ' ) : '' ?>
		</div>
		<?php
	}
}