<?php

function timetable_form_display() {
    $semester_arr = get_json( 'https://api.creol.ucf.edu/CoursesJson.asmx/SemesterList' );

    ob_start();
    ?>
    <div class="container">
        <div class="row">
            <form method="post" name="form" class="col">
                <div class="row">
                    <div class="form-group col-5">
                        <select name="semester" id="semester" class="form-control">
                            <?php for ( $i = 0; $i < count( $semester_arr ); $i++ ) : ?>
                                <option value="<?= $semester_arr[ $i ]->SemesterSerial ?>" 
                                <?= ( isset($_POST['semester']) && $_POST['semester'] == $semester_arr[ $i ]->SemesterSerial ) ? 'selected=true' : '' ?>>
                                    <?= $semester_arr[ $i ]->SemesterTxt ?>
                                </option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="col-4">
                        <div class="form-check">
                            <label class="form-check-label">
                                <input id="undergrad" class="form-check-input" type="checkbox" name="undergrad" value=1 
                                <?= ( isset($_POST['undergrad'] ) &&  $_POST['undergrad'] == 1 ) ? 'checked' : '' ?>>
                                Undergraduate
                            </label>
                        </div>
                        <div class="form-check">
                            <label class="form-check-label">
                                <input id="grad" class="form-check-input" type="checkbox" name="grad" value=0
                                <?= ( isset($_POST['grad'] ) && $_POST['grad'] == 0 ) ? 'checked' : '' ?>>
                                Graduate
                            </label>
                        </div>
                    </div>
                    <div class="col-3">
                        <button type="submit" name="submit" class="btn btn-primary">Submit</button>
                    </div>
                </div>
            </form>
            <div class="col-1">
			    <hr class="hr-vertical h-75">
		    </div>
            <div class="col-5">
                <span class="p-2" style="background-color: #99CCFF;">102</span><br>
                <span class="p-2" style="background-color: #66CCFF;">Online</span>
                <span class="p-2" style="background-color: #FFFF99;">103</span>
                <span class="p-2" style="background-color: #C0C0C0;">265</span>
                <span class="p-2" style="background-color: #FF9966;">266</span>
                <span class="p-2" style="background-color: #CC6699;">A207</span>
                <span class="p-2" style="background-color: #FF99FF;">A210</span>
                <span class="p-2" style="background-color: #D0FFD0;">A214</span>
                <span class="p-2" style="background-color: #99DD99;">Online</span>
            </div>
        </div>
    </div>
    <div class="container">
        <?php
        if ( isset( $_POST['semester'] ) && ( isset( $_POST['undergrad'] ) || isset( $_POST['grad'] ) ) ) {
            echo $_POST['semester'] . ' ' . $_POST['undergrad'] . ' ' . $_POST['grad'];
        } else {
        ?>
            <script>
			    // Sets the form to the correct information.
				document.getElementById("semester").selectedIndex = 0;
				document.getElementById("undergrad").checked = true;
				document.getElementById("grad").checked = true;
			</script>
        <?php
        }
        ?>
    </div>
    <?php
    return ob_get_clean();
}

function timetable_display( $semester, $level ) {
    
}