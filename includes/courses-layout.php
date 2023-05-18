<?php
/**
 * Handles the form and the output.
 **/

function courses_form_display() {
	$semester_arr = get_json( 'https://api.creol.ucf.edu/CoursesJson.asmx/SemesterList' );
	$instructor_arr = get_json( 'https://api.creol.ucf.edu/CoursesJson.asmx/InstructorList' );
	$course_arr = get_json( 'https://api.creol.ucf.edu/CoursesJson.asmx/CourseList' );

	ob_start();
	?>
	<div class="container">
		<div class="row">
			<!-- Form -->
			<div class="col-lg-3 col-12">
				<form method="get" name="form">
					<div class="form-group">
						<label for="semester">Semester</label>
						<select name="semester" id="semester" class="form-control" onchange="this.form.submit()">
							<option value=0>All</option>
							<?php for ( $i = 0; $i < count( $semester_arr ); $i++ ) : ?>
								<option value="<?= $semester_arr[ $i ]->SemesterSerial ?>">
									<?= $semester_arr[ $i ]->SemesterTxt ?>
								</option>
							<?php endfor; ?>
						</select>
					</div>
					<div class="form-group">
						<label for="instructor">Instructor</label>
						<select name="instructor" id="instructor" class="form-control" onchange="this.form.submit()">
							<option value=-1>All</option>
							<?php for ( $i = 0; $i < count( $instructor_arr ); $i++ ) : ?>
								<option value="<?= $instructor_arr[ $i ]->PeopleID ?>">
									<?= $instructor_arr[ $i ]->LastFirstName ?>
								</option>
							<?php endfor; ?>
						</select>
					</div>
					<div class="form-group">
						<label for="course">Course</label>
						<select name="course" id="course" class="form-control" onchange="this.form.submit()">
							<option value=0>All</option>
							<?php for ( $i = 0; $i < count( $course_arr ); $i++ ) : ?>
								<option value="<?= $course_arr[ $i ]->CourseID ?>">
									<?= $course_arr[ $i ]->FullCourseName ?>
								</option>
							<?php endfor; ?>
						</select>
					</div>
					<div class="form-group">
						<label for="level">Level</label>
						<select name="level" id="level" class="form-control" onchange="this.form.submit()">
							<option value="2">All</option>
							<option value="1">Undergraduate</option>
							<option value="0">Graduate</option>
						</select>
					</div>
					<br>
				</form>
				<a class="btn btn-primary" href="https://creolcmsdev.smca.ucf.edu/timetable/" target="_blank">
					Timetable View
				</a>
			</div>
			<!-- Course output from form selection -->
			<div class="col mt-lg-0 mt-5">
				<?php
				if ( isset( $_GET['semester'] ) && isset( $_GET['instructor'] ) && isset( $_GET['course'] ) && isset( $_GET['level'] ) ) {
					if ( $_GET['semester'] == 0 && $_GET['instructor'] == -1 && $_GET['course'] == 0 ) {
						echo 'Choose a semester, instructor, or course';
					} else {
						courses_display( $_GET['semester'], $_GET['instructor'], $_GET['course'], $_GET['level'] );
						?>
						<script>
							const urlParams = new URLSearchParams(window.location.search);
							document.getElementById("semester").value = urlParams.get("semester");
							document.getElementById("instructor").value = urlParams.get("instructor");
							document.getElementById("course").value = urlParams.get("course");
							document.getElementById("level").value = urlParams.get("level");
						</script>
						<?php
					}
				} else {
					courses_display( semester_serial(), -1, 0, 2 );
					?>
					<script>
						document.getElementById("semester").selectedIndex = 1;
					</script>
					<?php
				}
				?>
			</div>
		</div>
	</div>
	<?php
	return ob_get_clean();
}

function courses_display( $semester, $instructor, $course, $level ) {
	$url = 'https://api.creol.ucf.edu/CoursesJson.asmx/CourseInfo?Semester=' . $semester . '&Instructor=' . $instructor . '&CourseID=' . $course . '&Level=' . $level;
	$course_info_arr = get_json( $url );

	foreach ( $course_info_arr as $curr ) {
		?>
		<div class="px-2 pb-3">
			<span class="h-5 font-weight-bold letter-spacing-1">
				<?= $curr->Course . ' ' . $curr->Title ?>
			</span><br>
			<?= $semester == 0 ? ( $curr->Semester . ': ' ) : '' ?>
			<?= class_days( $curr->Mon, $curr->Tue, $curr->Wed, $curr->Thu, $curr->Fri ) . ' ' . $curr->StartTime . ' to ' . $curr->EndTime ?><br>
			<?= 'Room: ' . $curr->Room ?><br>
			| <a href="<?= instructor_url( $curr->FirstLastName ) ?>" target="_blank"><?= $curr->FirstLastName ?></a> |
			<?= $curr->isDetail ? ( '<a href="details/?courseid=' . $curr->CourseID . '">Details</a> | ' ) : '' ?>
			<?= $curr->isSyllabus ? ( '<a href="syllabus/?scheduleid=' . $curr->CourseScheduleID . '&course=' . $curr->Course . '">Syllabus</a> | ' ) : '' ?>
			<?= $curr->isWebCourse ? '<a href="https://webcourses.ucf.edu" target="_blank">Distance Learning</a> | ' : '' ?>
			<?= $curr->isWebSite ? '<a href="' . $curr->URL . '" target="_blank">Website</a> |' : '' ?><br>
		</div>
		<?php
	}
}