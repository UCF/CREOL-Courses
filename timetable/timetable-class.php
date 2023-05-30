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
		$this->url = $url;

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
		$courses = get_json( $this->url );

		// Handles the start time and end time of the table
		$this->start_time = self::get_row( end( $courses )->StartTime );
		$this->end_time = self::get_row( end( $courses )->EndTime );
		array_pop( $courses );

		foreach ( $courses as $course ) {
			$day = $course->DOW;

			// Stores previous values and resets.
			if ( $day != $prev_day ) {
				$col = $starting_col = $curr_total_cols = count( $this->table );
				$this->num_cols[ $prev_day ] = $curr_total_cols - $prev_total;

				$prev_total = $curr_total_cols;
				$prev_day = $day;
			}

			$start_row = self::get_row( $course->StartTime ) - $this->start_time;
			$end_row = self::get_row( $course->EndTime ) - $this->start_time;

			// Moves to the next column if a collision happens
			while ( isset( $this->table[ $col ][ $start_row ] ) ) {
				$col++;
			}

			$this->table[ $col ][ $start_row ] = $course;

			for ( $i = $start_row + 1; $i < $end_row; $i++ ) {
				$this->table[ $col ][ $i ] = 1;
			}

			// Adding 'Rowspan' property
			$course->Rowspan = $end_row - $start_row;

			$col = $starting_col;
		}

		$this->num_cols[ $day ] = count( $this->table ) - $prev_total; // Setting the last days number of columns
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

	private function add_border( $col ) {
		$adding_cols = array();
		$prev_val = 0;

		for ( $i = 1; $i <= self::DAYS_IN_SCHOOL_WEEK; $i++ ) {
			$adding_cols[] = $this->num_cols[ $i ] + $prev_val;
			$prev_val += $this->num_cols[ $i ];
		}
		echo var_dump( $adding_cols );

		if ( in_array( $col + 1, $adding_cols ) ) {
			return 'border-left:1px solid black';
		}
		return '';
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
		?>
		<table id="timetable" class="table table-sm table-responsive">
			<!-- Header -->
			<thead>
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
							// Course
							if ( isset( $this->table[ $c ][ $r ] ) ) {
								$curr_cell = $this->table[ $c ][ $r ];
								?>
								<?php if ( gettype( $curr_cell ) == 'object' ) : ?>
									<td rowspan="<?= $curr_cell->Rowspan ?>" class="line-height-1" style="
											font-size: 0.7rem; 
											background-color: <?= self::get_room_color( $curr_cell->CREOLRoomID, $curr_cell->isWebCourse ) ?>;
											<?= $this->is_hour( $r ) ? 'border-top:1px solid black;' : '' ?>
											<?= $this->add_border( $c ) ?>">
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
									<td style="border:1px 0px 0px 0px solid black;">&nbsp</td>
									<?php
								} else {
									?>
									<td class="border-0">&nbsp</td>
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