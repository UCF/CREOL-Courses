<?php
/*
Plugin Name: Courses
Plugin URI: https://github.com/UCF/CREOL-Courses/tree/dev
Description: Retrieves course information from database and displays as a list.
Author: Claire Daugherty
Version: 2.1
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

add_shortcode( 'courses', 'courses_list' );						// 	/courses
add_shortcode( 'course-schedule', 'courses_form_display' );		// /courses/course-schedule
add_shortcode( 'timetable', 'timetable_form_display' );			// /courses/timetable

// Used to add page templetes for Syllabus and Details pages
add_filter( 'theme_page_templates', 'add_page_template' );
add_filter( 'template_include', 'change_page_template', 99 );
