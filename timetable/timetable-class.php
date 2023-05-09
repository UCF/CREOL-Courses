<?php
/**
 * 
 */
class TimeTable {
    // properties
    public $url; 
    private $table;
    private $start_time = 32;
    private $end_time = 80;
    private $num_cols = array( PHP_INT_MIN, NULL, NULL, NULL, NULL, NULL, PHP_INT_MIN );

    // constructor
    function __construct( $url ) {
        $this->url = $url;
    }

    public function get_num_cols() {
        return $this->num_cols;
    }

    public function get_table() {
        $this->create_timetable();
        print("<pre>" . print_r( $this->table, true ) . "</pre>");
    }

    private static function get_row( $time ) {
        $time = strtotime( $time );
        $hour = idate( "H", $time );
        $min = idate( "i", $time );
        
        // return ( $hour * 2 ) + ceil( $min / 30 );
        return ( $hour * 4 ) + ceil( $min / 15 );
    }

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

    public function create_timetable() {
        $col = $starting_col = 0;
        $prev_day = 1;              // Monday
        $prev_total = 0;
        $courses = get_json( $this->url );
        $this->start_time = self::get_row( end( $courses )->StartTime );
        $this->end_time = self::get_row( end( $courses )->EndTime );
        array_pop( $courses );
        echo $this->start_time . ' ' . $this->end_time;

        foreach ( $courses as $course ) {
            $day = $course->DOW;

            if ( $day != $prev_day ) {
                $curr_total_cols = count( $this->table );
                
                $col = $starting_col = $curr_total_cols;
                $this->num_cols[$prev_day] = $curr_total_cols - $prev_total;

                $prev_total = count( $this->table );
                $prev_day = $day;
            }

            $start_row = self::get_row( $course->StartTime ) - $this->start_time;
            $end_row = self::get_row( $course->EndTime ) - $this->start_time;

            while ( isset( $this->table[$col][$start_row] ) ) {
                $col++;
            }

            $this->table[$col][$start_row] = $course;

            for ( $i = $start_row + 1; $i < $end_row; $i++ ) {
                $this->table[$col][$i] = 1;
            }

            // Adding 'Rowspan' property
            $course->Rowspan = $end_row - $start_row;

            $col = $starting_col;
        }

        $this->num_cols[$day] = count( $this->table ) - $prev_total; 
    }

    
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
    
    public function table_header() {
        ?>
        <table id="timetable" class="table table-sm table-bordered table-responsive">
            <thead>
                <tr class="bg-primary">
                    <th></th>
                    <?php for ( $i = 1; $i <= 5; $i++ ) : ?>
                        <th colspan="<?= $this->num_cols[$i] ?>" 
                        style="width:<?= 100 / count( $this->table ) ?>%;">
                        <?= self::get_day( $i ) ?>
                    </th>
                    <?php endfor; ?>
                </tr>
            </thead>
            <?php 
    }
    
    private function get_time( $row ) {
        $time = ( $row + $this->start_time ) * 15;
        return $time;
    }
    
    public function display() {
        $total_rows = $this->end_time - $this->start_time;
        $total_cols = count( $this->table );
        ?>
        <tbody>
        <?php
        for ( $r = 0; $r < $total_rows; $r++ ) {
            $time_text = ( $r % 4 == 0 ) ? $r : '&nbsp';
            ?>
            <tr>
                <!-- time sidebar -->
                <th scope="row" class="pt-0 font-size-sm" style="width:2.5%;"><?= $time_text . ' ' . $this->get_time($r) ?></th>
                <?php
                for ( $c = 0; $c < $total_cols; $c++ ) {
                    if ( isset( $this->table[$c][$r] ) ) {
                        $curr_cell = $this->table[$c][$r];
                        ?>
                        <?php if ( gettype( $curr_cell ) == 'object' ) : ?>
                        <td rowspan="<?= $curr_cell->Rowspan ?>"
                        class="line-height-1"
                        style="font-size: 0.7rem; background-color: 
                        <?= self::get_room_color( $curr_cell->CREOLRoomID, $curr_cell->isWebCourse ) ?>;">
                            <span class="font-weight-bold"><?= $curr_cell->Course . '<br>' . $curr_cell->Title ?></span>
                            <br>
                            <?= $curr_cell->StartTime ?> - <?= $curr_cell->EndTime ?>
                            <br>
                            <a href="<?= instructor_url( $curr_cell->FirstLastName ) ?>" target="_blank">
                                <?= $curr_cell->FirstLastName ?>
                            </a>
                        </td>
                        <?php endif; ?>
                        <?php 
                    } else {
                        ?>
                        <td>&nbsp</td>
                        <?php 
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