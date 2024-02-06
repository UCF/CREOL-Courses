<?php
/**
 * Handles the form and the output.
 **/

 // Handles the dropdown on the left.
function courses_form_display() {
	$semester_arr = get_json( 'https://api.creol.ucf.edu/CoursesJson.asmx/SemesterList' );
	$instructor_arr = get_json( 'https://api.creol.ucf.edu/CoursesJson.asmx/InstructorList' );
	$course_arr = get_json( 'https://api.creol.ucf.edu/CoursesJson.asmx/CourseList' );
	if ( is_null( $semester_arr ) || is_null( $instructor_arr ) || is_null( $course_arr ) ) {
		return false;
	}

	ob_start();
	?>
	<div class="container">
		<div class="row">
			<!-- Form -->
			<div class="col-lg-3 col-12">
				<form method="get" name="form">
					<div class="form-group">
						<label for="semester">Semester</label>
						<select name="semester" id="semester" class="form-control" onchange="handleSelectorChange()">
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
						<select name="instructor" id="instructor" class="form-control" onchange="handleSelectorChange()">
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
						<select name="course" id="course" class="form-control" onchange="handleSelectorChange()">
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
						<select name="level" id="level" class="form-control" onchange="handleSelectorChange()">
							<option value="2">All</option>
							<option value="1">Undergraduate</option>
							<option value="0">Graduate</option>
						</select>
					</div>
					<br>
				</form>
				<!-- Disable controls on input change -->
				<script>
					let form = document.getElementsByName("form")[0];
					let elements = form.elements;
					function handleSelectorChange() {
						for (let i = 0, len = elements.length; i < len; ++i) {
							elements[i].style.pointerEvents = "none";
							elements[i].onclick = () => false;
							elements[i].onkeydown = () => false;
							elements[i].style.backgroundColor = "#f0f0f0";
			            	elements[i].style.color = "#6c757d";
			            	elements[i].style.border = "1px solid #ced4da";
						}
						form.submit();
					}
				</script>
				<a class="btn btn-primary" href="/timetable/">
					Timetable View
				</a>
			</div>
			<!-- Course output from form selection -->
			<div class="col mt-lg-0 mt-5">
				<?php
				if ( isset( $_GET['semester'] ) && isset( $_GET['instructor'] ) && isset( $_GET['course'] ) && isset( $_GET['level'] ) ) {
					if ( $_GET['semester'] == ALL_SEMESTERS && $_GET['instructor'] == ALL_INSTRUCTORS && $_GET['course'] == ALL_COURSES ) {
						echo 'Choose a semester, instructor, or course';
					} else {
						courses_display( $_GET['semester'], $_GET['instructor'], $_GET['course'], $_GET['level'] );
						?>
						<!-- Setting the drop downs to match the selection -->
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
					courses_display( semester_serial(), ALL_INSTRUCTORS, ALL_COURSES, UNDERGRAD_GRAD );
					?>
					<script>
						document.getElementById("semester").value = <?= semester_serial() ?>;
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

	if ( is_null( $course_info_arr ) ) {
		return false;
	}

	foreach ( $course_info_arr as $curr ) {
		?>
		<div class="px-2 pb-3">
			<span class="h-5 font-weight-bold letter-spacing-1">
				<?= $curr->Course . ' ' . $curr->Title ?>
			</span><br>
			<?= $semester == ALL_SEMESTERS ? ( $curr->Semester . ': ' ) : '' ?>
			<?= class_days( $curr->Mon, $curr->Tue, $curr->Wed, $curr->Thu, $curr->Fri ) . ' ' . $curr->StartTime . ' to ' . $curr->EndTime ?><br>
			<?= 'Room: ' . $curr->Room ?><br>
			| <a href="<?= instructor_url( $curr->FirstLastName ) ?>" target="_blank"><?= $curr->FirstLastName ?></a> |
			<?= $curr->isDetail ? ( '<a href="details/?courseid=' . $curr->CourseID . '">Details</a> | ' ) : '' ?>
			<?= $curr->isSyllabus ? ( '<a href="https://api.creol.ucf.edu/ViewSyllabus.aspx/?CourseScheduleID=' . $curr->CourseScheduleID . '" target="_blank">Syllabus</a> | ' ) : '' ?>
			<?= $curr->isWebCourse ? '<a href="https://webcourses.ucf.edu" target="_blank">Distance Learning</a> | ' : '' ?>
			<?= $curr->isWebSite ? '<a href="' . $curr->URL . '" target="_blank">Website</a> |' : '' ?><br>
		</div>
		<?php
	}
}
