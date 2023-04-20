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
			<div class="col-3">
				<form method="post" name="form">
					<div class="form-group">
						<label for="semester">Semester</label>
						<select name="semester" id="semester" class="form-control">
							<option value=0>All</option>
							<?php for ( $i = 0; $i < count( $semester_arr ); $i++ ) : ?>
								<option value="<?= $semester_arr[ $i ]->SemesterSerial ?>" 
								<?= ( isset($_POST['semester']) && $_POST['semester'] == $semester_arr[ $i ]->SemesterSerial ) ? 'selected=true' : '' ?>>
									<?= $semester_arr[ $i ]->SemesterTxt ?>
								</option>
							<?php endfor; ?>
						</select>
					</div>
					<div class="form-group">
						<label for="instructor">Instructor</label>
						<select name="instructor" id="instructor" class="form-control">
							<option value=-1>All</option>
							<?php for ( $i = 0; $i < count( $instructor_arr ); $i++ ) : ?>
								<option value="<?= $instructor_arr[ $i ]->PeopleID ?>"
								<?= ( isset($_POST['instructor']) && $_POST['instructor'] == $instructor_arr[ $i ]->PeopleID ) ? 'selected=true' : '' ?>>
									<?= $instructor_arr[ $i ]->LastFirstName ?>
								</option>
							<?php endfor; ?>
						</select>
					</div>
					<div class="form-group">
						<label for="course">Course</label>
						<select name="course" id="course" class="form-control">
							<option value=0>All</option>
							<?php for ( $i = 0; $i < count( $course_arr ); $i++ ) : ?>
								<option value="<?= $course_arr[ $i ]->CourseID ?>"
								<?= ( isset($_POST['course']) && $_POST['course'] == $course_arr[ $i ]->CourseID ) ? 'selected=true' : '' ?>>
									<?= $course_arr[ $i ]->FullCourseName ?>
								</option>
							<?php endfor; ?>
						</select>
					</div>
					<!-- <div class="form-group">
						<label for="level">Level</label>
						<select id="level" name="level" class="form-control">
							<option value=2 <?= ( isset($_POST['level']) && $_POST['level'] == 2 ) ? 'selected=true' : '' ?>>All</option>
							<option value=1 <?= ( isset($_POST['level']) && $_POST['level'] == 1 ) ? 'selected=true' : '' ?>>Undergraduate</option>
							<option value=0 <?= ( isset($_POST['level']) && $_POST['level'] == 0 ) ? 'selected=true' : '' ?>>Graduate</option>
						</select>
					</div> -->
					<div class="form-check">
						<label class="form-check-label">
							<input class="form-check-input" type="checkbox" name="undergrad" value=1 
							<?= ( isset($_POST['level']) && ( $_POST['level'] == 2 || $_POST['level'] == 1 ) ) ? 'checked' : '' ?>>
							 Undergraduate
						</label>
					</div>
					<div class="form-check">
						<label class="form-check-label">
							<input class="form-check-input" type="checkbox" name="grad" value=0
							<?= ( isset($_POST['level']) && ( $_POST['level'] == 2 || $_POST['level'] == 0 ) ) ? 'checked' : '' ?>>
							 Graduate
						</label>
					</div>
					<br>
					<button type="submit" name="submit" class="btn btn-primary">Submit</button>
				</form>
			</div>
			<div class="col">
				<?php
				if ( isset( $_POST['semester'] ) && isset( $_POST['instructor'] ) && isset( $_POST['course'] ) && ( isset( $_POST['undergrad'] ) || isset( $_POST['grad'] ) ) ) {
					$semester = $_POST['semester'];
					$instructor = $_POST['instructor'];
					$course = $_POST['course'];
					$level = get_level( $_POST['undergrad'], $_POST['grad'] );

					if ( $semester == 0 && $instructor == -1 && $course == 0 ) {
						echo 'Select a semester, instructor, or course';
					} else {
						if ( has_filter( 'courses_display' ) ) {
							echo apply_filters( 'courses_display', $semester, $instructor, $course, $level );
						}
					}
				} else {
					echo apply_filters( 'courses_display', semester_serial(), -1, 0, 2 );
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

	ob_start();
	foreach ( $course_info_arr as $curr ) {
		?>
		<div class="px-2 pb-3">
			<span class="h-5 font-weight-bold letter-spacing-1">
				<?= $curr->Course . ' ' . $curr->Title ?>
			</span><br>
			<?= class_days( $curr->Mon, $curr->Tue, $curr->Wed, $curr->Thu, $curr->Fri ) . ' ' . $curr->StartTime . ' to ' . $curr->EndTime ?><br>
			<?= 'Location: ' . $curr->Room ?><br>
			| <a href="<?= instructor_url( $curr->FirstLastName ) ?>" target="_blank"><?= $curr->FirstLastName ?></a> | 
			<?= $curr->isDetail ? ( '<a href="details/?courseid=' . $curr->CourseID . '">Details</a> | ' ) : '' ?>
			<?= $curr->isSyllabus ? ( '<a href="syllabus/?scheduleid=' . $curr->CourseScheduleID . '&course=' . $curr->Course . '">Syllabus</a> | ' ) : '' ?>
			<?= $curr->isWebCourse ? '<a href="https://webcourses.ucf.edu" target="_blank">Distance Learning</a> | ' : '' ?>
			<?= $curr->isWebSite ? '<a href="' . $curr->URL . '" target="_blank">Website</a> |' : '' ?><br>
		</div>
		<?php
	}

	return ob_get_clean();
}
add_filter( 'courses_display', 'courses_display', 10, 4 );