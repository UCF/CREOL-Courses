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

require_once 'includes/courses-feed.php';
require_once 'includes/courses-layout.php';
require_once 'includes/courses-functions.php';
// require_once 'templates/template-functions.php';

add_shortcode( 'courses', 'courses_form_display' );
// add_filter( 'theme_page_templates', 'add_page_template' );
// add_filter( 'template_include', 'change_page_template', 99 );
