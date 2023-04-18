<?php
function add_page_template( $templates ) {
	$templates['details-template.php'] = __( 'Course Details Template', 'text-domain' );
	$templates['syllabus-template.php'] = __( 'Course Syllabus Template', 'text-domain' );

	return $templates;
}

function change_page_template( $template ) {
	if ( is_page() ) {
		if ( get_page_template_slug() === 'details-template.php' ) {
			$template = plugin_dir_path( __FILE__ ) . 'details-template.php';
		}

		if ( get_page_template_slug() === 'syllabus-template.php' ) {
			$template = plugin_dir_path( __FILE__ ) . 'syllabus-template.php';
		}
	}

	return $template;
}