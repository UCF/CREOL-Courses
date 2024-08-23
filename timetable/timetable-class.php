<?php
/**
 * 
 */
class TimeTable {
	// constants
	const NUM_CELLS_IN_HOUR = 4;
	const VALUE_OF_CELL = 25;
	const DAYS_IN_SCHOOL_WEEK = 5;
	const SECONDS_IN_HOUR = 3600;

	// properties
	public $url;
	private $table;
	private $start_time = 32;
	private $end_time = 80;
	private $num_cols = array( PHP_INT_MIN, NULL, NULL, NULL, NULL, NULL, PHP_INT_MIN );

	// constructor
	function __construct( $url ) {
		error_log("FED URL: " . $url);
		$this->url = $url;
		error_log("this->url: " . $this->url);

		$this->create_timetable();
	}

	/**
	 * Returns row based on time.
	 * @param mixed $time
	 * @return float
	 */
	private static function get_row( $time ) {
		$time = strtotime( $time );
		$hour = idate( "H", $time );
		$min = idate( "i", $time );

		return ( $hour * self::NUM_CELLS_IN_HOUR ) + ceil( $min / 15 );
	}

	/**
	 * Returns day based on number where Monday is 1.
	 * @param int $day
	 * @return string
	 */
	private static function get_day( $day ) {
		switch ( $day ) {
			case 1:
				$day_name = 'Monday';
				break;
			case 2:
				$day_name = 'Tuesday';
				break;
			case 3:
				$day_name = 'Wednesday';
				break;
			case 4:
				$day_name = 'Thursday';
				break;
			case 5:
				$day_name = 'Friday';
				break;
		}

		return $day_name;
	}

	/**
	 * 
	 * @return void
	 */
	private function create_timetable() {
		$col = $starting_col = 0;
		$prev_day = 1; // Monday
		$prev_total = 0;
		error_log("Before JSON call: " . $this->url);
		$courses = get_json($this->url);
		if (is_array($courses) || is_object($courses)) {
			error_log("Courses: " . print_r($courses, true));
		} else {
			error_log("Unexpected courses format: " . var_export($courses, true));
		}
		if ( is_null( $courses ) ) {
			return;
		}
	
		// Debugging: Log API response
		error_log("Courses: " . print_r($courses, true));
	
		$this->start_time = self::get_row( end( $courses )->StartTime );
		$this->end_time = self::get_row( end( $courses )->EndTime );
		array_pop( $courses );
	
		foreach ( $courses as $course ) {
			$day = $course->DOW;
	
			if ( $day != $prev_day ) {
				$col = $starting_col = $curr_total_cols = count( $this->table );
				$this->num_cols[ $prev_day ] = $curr_total_cols - $prev_total;
	
				$prev_total = $curr_total_cols;
				$prev_day = $day;
			}
	
			$start_row = self::get_row( $course->StartTime ) - $this->start_time;
			$end_row = self::get_row( $course->EndTime ) - $this->start_time;
	
			// Debugging: Log course details
			error_log("Course: " . print_r($course, true) . " Start Row: $start_row, End Row: $end_row");
	
			while ( isset( $this->table[ $col ][ $start_row ] ) ) {
				$col++;
			}
	
			$this->table[ $col ][ $start_row ] = $course;
	
			for ( $i = $start_row + 1; $i < $end_row; $i++ ) {
				$this->table[ $col ][ $i ] = 1;
			}
	
			$course->Rowspan = $end_row - $start_row;
	
			$col = $starting_col;
		}
	
		$this->num_cols[ $day ] = count( $this->table ) - $prev_total; // Setting the last days number of columns
	
		// Debugging: Log final table setup
		error_log("Final Table: " . print_r($this->table, true));
	}
	

	private function get_cumulative_cols() {
		$adding_cols = array();
		$prev_total = 0;

		for ( $i = 1; $i < self::DAYS_IN_SCHOOL_WEEK; $i++ ) {
			$adding_cols[] = ( $this->num_cols[ $i ] + $prev_total ) - 1;
			$prev_total += $this->num_cols[ $i ];
		}

		return $adding_cols;
	}

	/**
	 * Returns hex for background color of course. Uses the room ID to determine color.
	 * @param string $room
	 * @param bool $is_webcourse
	 * @return string
	 */
	private static function get_room_color( $room, $is_webcourse ) {
		switch ( $room ) {
			case 4:
				$color = ( $is_webcourse ) ? '#66CCFF' : '#99CCFF';
				break;
			case 5:
				$color = '#FFFF99';
				break;
			case 157:
				$color = '#C0C0C0';
				break;
			case 158:
				$color = '#FF9966';
				break;
			case 222:
				$color = '#CC6699';
				break;
			case 225:
				$color = '#FF99FF';
				break;
			case 229:
				$color = ( $is_webcourse ) ? '#99DD99' : '#D0FFD0';
				break;
			default:
				$color = '#FFCCCB';
		}

		return $color;
	}

	/**
	 * Returns true if the row is on the hour.
	 * @param int $row
	 * @return bool
	 */
	private function is_hour( $row ) {
		return ( $row + $this->start_time ) % self::NUM_CELLS_IN_HOUR == 0;
	}

	/**
	 * Outputs the time sidebar on the left.
	 * @param int $row
	 * @return void
	 */
	private function get_time( $row ) {
		if ( $this->is_hour( $row ) ) {
			$time = ( $row + $this->start_time ) * self::VALUE_OF_CELL;
			$time = substr( $time, 0, -2 ) * self::SECONDS_IN_HOUR; // date format handles time in seconds
			$time = date( 'g:i', $time );
			?>
			<th scope="row" class="font-size-sm border-left-0 border-bottom-0 border-right-0"
				style="width:2.5%; border-top:1px solid black;">
				<?= $time ?>
			</th>
			<?php
		} else {
			$time = '&nbsp';
			?>
			<th scope="row" class="font-size-sm border-0" style="width:2.5%">
				<?= $time ?>
			</th>
			<?php
		}
	}

	/**
	 * Displays courses as table.
	 * @return void
	 */
	public function display() {
		$total_rows = $this->end_time - $this->start_time;
		$total_cols = count( $this->table );

		$border_cols = $this->get_cumulative_cols();
		?>
		<table id="timetable" class="table table-sm table-responsive">
			<!-- Header -->
			<thead class="sticky-top">
				<tr class="bg-primary">
					<th style="border-bottom:1px solid black;"></th>
					<?php for ( $i = 1; $i <= self::DAYS_IN_SCHOOL_WEEK; $i++ ) : ?>
						<?php if ( ! $this->num_cols[ $i ] < 1 ) : ?>
							<th colspan="<?= $this->num_cols[ $i ] ?>"
								style="width:<?= 100 / count( $this->table ) ?>%;border-bottom:1px solid black;">
								<?= self::get_day( $i ) ?>
							</th>
						<?php endif; ?>
					<?php endfor; ?>
				</tr>
			</thead>
			<tbody>
				<?php
				for ( $r = 0; $r < $total_rows; $r++ ) {
					?>
					<tr>
						<?php
						$this->get_time( $r ); // Time sidebar
			
						for ( $c = 0; $c < $total_cols; $c++ ) {
							if ( in_array( $c, $border_cols ) ) {
								$border = 'border-right:1px solid black;';
							} else {
								$border = '';
							}

							// Course
							if ( isset( $this->table[ $c ][ $r ] ) ) {
								$curr_cell = $this->table[ $c ][ $r ];
								?>
								<?php if ( gettype( $curr_cell ) == 'object' ) : ?>
									<td rowspan="<?= $curr_cell->Rowspan ?>" class="line-height-1" style="
											font-size: 0.7rem; 
											background-color: <?= self::get_room_color( $curr_cell->CREOLRoomID, $curr_cell->isWebCourse ) ?>;
											<?= $this->is_hour( $r ) ? 'border-top:1px solid black;' : '' ?><?= $border ?>">
										<span class="font-weight-bold">
											<?= $curr_cell->Course . '<br>' . $curr_cell->Title ?>
										</span>
										<br>
										<?= $curr_cell->StartTime ?> -
										<?= $curr_cell->EndTime ?>
										<br>
										<a href="<?= instructor_url( $curr_cell->FirstLastName ) ?>" target="_blank">
											<?= $curr_cell->FirstLastName ?>
										</a>
									</td>
								<?php endif; ?>
								<?php
							} else {
								// Styling for empty cells
								if ( $this->is_hour( $r ) ) {
									?>
									<td style="border-top:1px solid black;<?= $border ?>">&nbsp</td>
									<?php
								} else {
									?>
									<td class="border-top-0" style="<?= $border ?>">&nbsp</td>
									<?php
								}
							}
						}
						?>
					</tr>
					<?php
				}
				?>
			</tbody>
		</table>
		<?php
	}
}