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
								<option value="<?= $semester_arr[ $i ]->SemesterSerial ?>"><?= $semester_arr[ $i ]->SemesterTxt ?></option>
							<?php endfor; ?>
						</select>
					</div>
					<div class="form-group">
						<label for="instructor">Instructor</label>
						<select name="instructor" id="instructor" class="form-control">
							<option value=-1>All</option>
							<?php for ( $i = 0; $i < count( $instructor_arr ); $i++ ) : ?>
								<option value="<?= $instructor_arr[ $i ]->PeopleID ?>"><?= $instructor_arr[ $i ]->LastFirstName ?></option>
							<?php endfor; ?>
						</select>
					</div>
					<div class="form-group">
						<label for="course">Course</label>
						<select name="course" id="course" class="form-control">
							<option value=0>All</option>
							<?php for ( $i = 0; $i < count( $course_arr ); $i++ ) : ?>
								<option value="<?= $course_arr[ $i ]->CourseID ?>"><?= $course_arr[ $i ]->FullCourseName ?></option>
							<?php endfor; ?>
						</select>
					</div>
					<div class="form-group">
						<label for="level">Level</label>
						<select id="level" name="level" class="form-control">
							<option value=2>All</option>
							<option value=1>Undergraduate</option>
							<option value=0>Graduate</option>
						</select>
					</div>
					<button type="submit" name="submit" class="btn btn-primary">Submit</button>
				</form>
			</div>
			<div class="col">
				<?php
				if ( isset( $_POST['semester'] ) && isset( $_POST['instructor'] ) && isset( $_POST['course'] ) && isset( $_POST['level'] ) ) {
					$semester = $_POST['semester'];
					$instructor = $_POST['instructor'];
					$course = $_POST['course'];
					$level = $_POST['level'];
					if ( $semester == 0 && $instructor == -1 && $course == 0 ) {
						echo 'Select a semester, instructor, or course';
					} else {
						if ( has_filter( 'courses_display' ) ) {
							echo apply_filters( 'courses_display', $semester, $instructor, $course, $level );
						}
					}
				} else {
					echo semester_serial();
					echo apply_filters( 'courses_display', semester_serial(), -1, 0, 2 );
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
			<a href="<?= instructor_url( $curr->FirstLastName ) ?>" target="_blank"><?= $curr->FirstLastName ?></a>
			<?= $curr->isDetail ? ( '<a href="details/?courseid=' . $curr->CourseID . '">Details</a>' ) : '' ?>
			<?= $curr->isSyllabus ? ( '<a href="syllabus/?scheduleid=' . $curr->CourseScheduleID . '&course=' . $curr->Course . '">Syllabus</a>' ) : '' ?>
			<?= $curr->isWebCourse ? '<a href="https://webcourses.ucf.edu" target="_blank">Distance Learning</a>' : '' ?>
			<?= $curr->isWebSite ? '<a href="' . $curr->URL . '" target="_blank">Website</a>' : '' ?><br>
		</div>
		<?php
	}

	return ob_get_clean();
}
add_filter( 'courses_display', 'courses_display', 10, 4 );