<?php
/**
 * 
 **/

function courses_form_display() {
	$semester_arr = get_json( 'https://api.creol.ucf.edu/CoursesJson.asmx/SemesterList' );
	$instructor_arr = get_json( 'https://api.creol.ucf.edu/CoursesJson.asmx/InstructorList' );
	$course_arr = get_json( 'https://api.creol.ucf.edu/CoursesJson.asmx/CourseList' );

	ob_start();
	?>
	<div>
		<form method="post" name="form">
			<select name="semester">
				<option value=0>All</option>
				<?php for ( $i = 0; $i < count( $semester_arr ); $i++ ) : ?>
					<option value="<?= $semester_arr[ $i ]->SemesterSerial ?>"><?= $semester_arr[ $i ]->SemesterTxt ?></option>
				<?php endfor; ?>
			</select>
			<select name="instructor">
				<option value=-1>All</option>
				<?php for ( $i = 0; $i < count( $instructor_arr ); $i++ ) : ?>
					<option value="<?= $instructor_arr[ $i ]->PeopleID ?>"><?= $instructor_arr[ $i ]->LastFirstName ?></option>
				<?php endfor; ?>
			</select>
			<select name="course">
				<option value=0>All</option>
				<?php for ( $i = 0; $i < count( $course_arr ); $i++ ) : ?>
					<option value="<?= $course_arr[ $i ]->CourseID ?>"><?= $course_arr[ $i ]->FullCourseName ?></option>
				<?php endfor; ?>
			</select>
			<select id="level" name="level">
				<option value=2>All</option>
				<option value=1>Undergraduate</option>
				<option value=0>Graduate</option>
			</select>
			<input name="submit" type="submit">
		</form>
	</div>
	<div>
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
			echo apply_filters( 'courses_display', semester_serial(), -1, 0, 2 );
		}
		?>
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
		</div>
		<?php
	}

	return ob_get_clean();
}
add_filter( 'courses_display', 'courses_display', 10, 4 );