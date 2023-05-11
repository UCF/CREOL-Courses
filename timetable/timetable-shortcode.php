<?php

function timetable_form_display() {
	$semester_arr = get_json( 'https://api.creol.ucf.edu/CoursesJson.asmx/SemesterList' );

	ob_start();
	?>
	<div class="container">
		<div class="row">
			<form method="post" name="form" id="timetableform" class="form-inline">
				<div class="form-group m-2">
					<select name="semester" id="semester" class="form-control" onchange="this.form.submit()">
						<?php for ( $i = 0; $i < count( $semester_arr ); $i++ ) : ?>
							<option value="<?= $semester_arr[ $i ]->SemesterSerial ?>" <?= ( isset( $_POST['semester'] ) && $_POST['semester'] == $semester_arr[ $i ]->SemesterSerial ) ? 'selected=true' : '' ?>>
								<?= $semester_arr[ $i ]->SemesterTxt ?>
							</option>
						<?php endfor; ?>
					</select>
				</div>
				<div class="form-check m-2">
					<label class="form-check-label">
						<input id="undergrad" class="form-check-input" type="checkbox" name="undergrad" value=1
							onclick="this.form.submit()">
						Undergraduate
					</label>
				</div>
				<div class="form-check m-2">
					<label class="form-check-label">
						<input id="grad" class="form-check-input" type="checkbox" name="grad" value=0
							onclick="this.form.submit()">
						Graduate
					</label>
				</div>
			</form>
			<div class="col-auto m-2">
				<a href="/courses/" class="btn btn-primary">Courses</a>
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
	<div style="padding: 1% 5% 5% 5%">
		<?php
		if ( isset( $_POST['semester'] ) && ( isset( $_POST['undergrad'] ) || isset( $_POST['grad'] ) ) ) {
			// Determines what to set level to
			if ( isset( $_POST['undergrad'] ) && ! isset( $_POST['grad'] ) ) {
				$level = $_POST['undergrad'];
				?>
				<script>
					document.getElementById("undergrad").checked = true;
				</script>
				<?php
			} else if ( ! isset( $_POST['undergrad'] ) && isset( $_POST['grad'] ) ) {
				$level = $_POST['grad'];
				?>
					<script>
						document.getElementById("grad").checked = true;
					</script>
				<?php
			} else {
				$level = 2;
				?>
					<script>
						// Sets the form to the correct information.
						document.getElementById("undergrad").checked = true;
						document.getElementById("grad").checked = true;
					</script>
				<?php
			}

			if ( has_filter( 'timetable_display' ) ) {
				echo apply_filters( 'timetable_display', $_POST['semester'], $level );
			}
		} else {
			?>
			<script>
				// Sets the form to the correct information.
				document.getElementById("semester").selectedIndex = 0;
				document.getElementById("undergrad").checked = true;
				document.getElementById("grad").checked = true;
			</script>
			<?php
			echo apply_filters( 'timetable_display', semester_serial(), 2 );
		}
		?>
	</div>
	<?php
	return ob_get_clean();
}

function timetable_display( $semester, $level ) {
	$url = 'https://api.creol.ucf.edu/CoursesJson.asmx/TimeTableInfo?Semester=' . $semester . '&Level=' . $level;
	$timetable = new Timetable( $url );
	$timetable->create_timetable();

	ob_start();
	$timetable->table_header();
	$timetable->display();

	return ob_get_clean();
}
add_filter( 'timetable_display', 'timetable_display', 10, 2 );