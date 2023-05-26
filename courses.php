<?php
/*
Plugin Name: Courses
Plugin URI: https://github.com/UCF/CREOL-Courses/tree/dev
Description: Retrieves course information from database and displays as a list.
Author: Claire Daugherty
Version: 1.0
Author URI: https://github.com/claire-md
*/

if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'ALL_SEMESTERS', 0 );
define( 'ALL_INSTRUCTORS', -1 );
define( 'ALL_COURSES', 0 );
define( 'UNDERGRAD_GRAD', 2 );
define( 'UNDERGRAD', 1 );
define( 'GRAD', 0 );

require_once 'includes/courses-feed.php';
require_once 'includes/courses-layout.php';
require_once 'includes/courses-functions.php';
require_once 'includes/courses-list.php';
require_once 'timetable/timetable-shortcode.php';
require_once 'timetable/timetable-class.php';
require_once 'templates/template-functions.php';

add_shortcode( 'courses', 'courses_list' );
add_shortcode( 'course-schedule', 'courses_form_display' );
add_shortcode( 'timetable', 'timetable_form_display' );
add_filter( 'theme_page_templates', 'add_page_template' );
add_filter( 'template_include', 'change_page_template', 99 );