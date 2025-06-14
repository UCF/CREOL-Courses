<?php

/*
Displays the form and initializes the class
*/

function timetable_form_display() {
	$semester_arr = get_json( 'https://api.creol.ucf.edu/CoursesJson.asmx/SemesterList' );
	?>
	<div class="container">
		<div class="row">
			<form method="get" class="form-inline">
				<div class="form-group m-2">
					<select name="semester" id="semester" class="form-control" aria-label="Select Semester" onchange="this.form.submit()">
						<?php for ( $i = 0; $i < count( $semester_arr ); $i++ ) : ?>
							<option value="<?= $semester_arr[ $i ]->SemesterSerial ?>">
								<?= $semester_arr[ $i ]->SemesterTxt ?>
							</option>
						<?php endfor; ?>
					</select>
				</div>
				<div class="form-group m-2">
					<select name="level" id="level" class="form-control" aria-label="Select Level" onchange="this.form.submit()">
						<option value="2">All</option>
						<option value="1">Undergraduate</option>
						<option value="0">Graduate</option>
					</select>
				</div>
			</form>
			<div class="col-auto m-2">
				<a href="/courses/course-schedule/" class="btn btn-primary">List View</a>
			</div>
			<div class="col-auto">
				<hr class="hr-vertical">
			</div>
			<div class="col m-2">
				<div class="row">
					<div class="col" style="background-color: #99CCFF;">102</div>
					<div class="col" style="background-color: #66CCFF;">Online</div>
					<div class="col" style="background-color: #FFFF99;">103</div>
					<div class="col" style="background-color: #C0C0C0;">265</div>
					<div class="col" style="background-color: #FF9966;">266</div>
					<div class="w-100"></div>
					<div class="col" style="background-color: #CC6699;">A207</div>
					<div class="col" style="background-color: #FF99FF;">A210</div>
					<div class="col" style="background-color: #D0FFD0;">A214</div>
					<div class="col" style="background-color: #99DD99;">Online</div>
				</div>
			</div>
		</div>
	</div>
	<?php
	ob_start();

	// Handles filter parameters 
	if ( isset( $_GET['semester'] ) && isset( $_GET['level'] ) ) {
		timetable_display( $_GET['semester'], $_GET['level'] );
		?>
		<script>
			const urlParams = new URLSearchParams(window.location.search);
			document.getElementById("semester").value = urlParams.get("semester");
			document.getElementById("level").value = urlParams.get("level");
		</script>
		<?php
	} else {
		timetable_display( semester_serial(), UNDERGRAD_GRAD );
		?>
		<script>
			document.getElementById("semester").value = <?= semester_serial() ?>;
		</script>
		<?php
	}

	return ob_get_clean();
}

// Parent function 
// Initializes new Timetable object and dsiplays it
function timetable_display( $semester, $level ) {
	$url = 'https://api.creol.ucf.edu/CoursesJson.asmx/TimeTableInfo?Semester=' . $semester . '&Level=' . $level;
	error_log('API URL: ' . $url);
	$timetable = new Timetable( $url );

	?>
	<div style="padding: 1% 5% 5% 5%">
		<?php $timetable->display(); ?>
	</div>
	<?php
}