<?php
/*
Plugin Name: Courses
Plugin URI: http://na/
Description: Retrieves course information from database and displays as a list.
Author: Claire Daugherty
Version: 1.0
Author URI: http://na/
*/
include_once 'includes/courses-functions.php';

add_shortcode( 'courses', 'callback' );