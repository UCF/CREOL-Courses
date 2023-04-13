<?php
function callback() {
	$semester_arr = get_json( 'https://www2.qa.creol.ucf.edu/CoursesJson.asmx/SemesterList' );
	$instructor_arr = get_json( 'https://www2.qa.creol.ucf.edu/CoursesJson.asmx/InstructorList' );
	$course_arr = get_json( 'https://www2.qa.creol.ucf.edu/CoursesJson.asmx/CourseList' );

	ob_start();
	?>
	<div>
		<form method="post" name="form">
			<select name="semester">
				<option value=0>All</option>
				<?php
				for ( $i = 0; $i < count( $semester_arr ); $i++ ) {
					$semester_selected = ( $_POST['semester'] == $semester_arr[ $i ]['SemesterSerial'] ) ? ' selected=true' : '';
					echo '<option value=' . $semester_arr[ $i ]['SemesterSerial'] . $semester_selected . '>' . $semester_arr[ $i ]['SemesterTxt'] . '</option>';
				}
				?>
			</select>
			<select name="instructor">
				<option value=-1>All</option>
				<?php
				for ( $i = 0; $i < count( $instructor_arr ); $i++ ) {
					$instructor_selected = ( $_POST['instructor'] == $instructor_arr[ $i ]['PeopleID'] ) ? ' selected=true' : '';
					echo '<option value=' . $instructor_arr[ $i ]['PeopleID'] . $instructor_selected . '>' . $instructor_arr[ $i ]['LastFirstName'] . '</option>';
				}
				?>
			</select>
			<select name="course">
				<option value=0>All</option>
				<?php
				for ( $i = 0; $i < count( $course_arr ); $i++ ) {
					$course_selected = ( $_POST['course'] == $course_arr[ $i ]['CourseID'] ) ? ' selected=true' : '';
					echo '<option value=' . $course_arr[ $i ]['CourseID'] . $course_selected . '>' . $course_arr[ $i ]['FullCourseName'] . '</option>';
				}
				?>
			</select>
			<select name="level">
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
				echo get_course_info( $semester, $instructor, $course, $level );
			}
		} else {
			echo get_course_info( semester_serial(), -1, 0, 2 );
		}
		?>
	</div>
	<?php
	return ob_get_clean();
}

function get_course_info( $semester, $instructor, $course, $level ) {
	$url = 'https://www2.qa.creol.ucf.edu/CoursesJson.asmx/CourseInfo?Semester=' . $semester . '&Instructor=' . $instructor . '&CourseID=' . $course . '&Level=' . $level;
	$course_info_arr = get_json( $url );
	foreach ( $course_info_arr as $curr ) {
		?>
		<div class="px-2 pb-3">
			<span class="h-5 font-weight-bold letter-spacing-1">
				<?= $curr['Course'] . ' ' . $curr['Title'] ?>
			</span><br>
			<?= class_days( $curr['Mon'], $curr['Tue'], $curr['Wed'], $curr['Thu'], $curr['Fri'] ) . ' ' . $curr['StartTime'] . ' to ' . $curr['EndTime'] ?><br>
			<?= location( $curr['Room'] ) ?><br>
			<a href="<?= instructor_url( $curr['FirstLastName'] ) ?>" target="_blank"><?= $curr['FirstLastName'] ?></a>
			<?= $curr['isDetail'] ? ( '<a href="/testing/courses/includes/courses-details.php?courseid=' . $curr['CourseID'] . '">Details</a>' ) : '' ?>
			<?= $curr['isSyllabus'] ? ( '<a href="/testing/includes/courses-syllabus.php?scheduleid=' . $curr['CourseScheduleID'] . '">Syllabus</a>' ) : '' ?>
			<?= $curr['isWebCourse'] ? '<a href="https://webcourses.ucf.edu" target="_blank">Distance Learning</a>' : '' ?>
			<?= $curr['isWebSite'] ? '<a href="' . $curr['URL'] . '" target="_blank">Website</a>' : '' ?><br><br>
		</div>
		<?php
	}
}

function semester_serial() {
	return ( date( 'Y' ) - 1980 ) * 3 + intdiv( ( date( 'm' ) - 1 ), 4 );
}

// Retrieves json from api and returns as an array.
function get_json( $url ) {
	$request = wp_remote_get( $url, array( 'sslverify' => false ) );

	if ( is_wp_error( $request ) ) {
		echo 'error';
		return false;
	}

	$decoded_json = json_decode( wp_remote_retrieve_body( $request ), true );

	return $decoded_json['response'];
}

/**
 * Helper functions that format the course info.
 */
// Formats the class days.
function class_days( $mon, $tue, $wed, $thu, $fri ) {
	if ( $mon || $tue || $wed || $thu || $fri ) {
		return ( $mon ? 'Mondays, ' : '' ) . ( $tue ? 'Tuesdays, ' : '' ) . ( $wed ? 'Wednesdays, ' : '' ) . ( $thu ? 'Thursdays, ' : '' ) . ( $fri ? 'Fridays, ' : '' );
	}
}

// Adds "CREOL" to room number.
function location( $room ) {
	if ( $room ) {
		return ( str_starts_with( $room, 'A' ) || is_numeric( $room ) ) ? ( 'CREOL ' . $room ) : $room;
	}
}

// Returns the instructor's URL by taking in their first and last name.
function instructor_url( $name ) {
	if ( $name ) {
		$find = [ '.', ' ', '"' ];
		$replace = [ '', '-', '' ];
		return 'https://creol.ucf.edu/person/' . str_replace( $find, $replace, $name );
	}
}