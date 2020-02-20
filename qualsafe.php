<?php
/*
Plugin Name: Qualsafe test
Description: Creates filtered list of Qualsafe courses and students from external url
Version: 1.0.0
Author: Perry Bonewell
*/

// init shortcodes
add_action( 'init', 'register_shortcodes' );

// add custom scripts and styles
function add_qualsafetest_scripts() {
	wp_register_script( 'qualsafetest_script', plugins_url( 'js/qsplugin.js', __FILE__ ), array( 'jquery' ), '1.0', true );
	wp_enqueue_script( 'qualsafetest_script' );
 
	wp_enqueue_style( 'qualsafetest_styles', plugins_url( 'css/qsstyle.css', __FILE__ ), '', '1.0' );
}

add_action( 'wp_enqueue_scripts', 'add_qualsafetest_scripts' );

// create shortcode function
function display_course_list ( $atts ) {
	// get course data url from shortcode
    extract( shortcode_atts ( array(
        'courselist_url' => ''
    ), $atts ) );
 
	 	// get html body
	    $http_all 	= wp_remote_get( $courselist_url );
	   	$http_body 	= wp_remote_retrieve_body( $http_all );

	   	// clean up invalid json - tested data url at https://jsonlint.com/
	   	// needs better regex to remove any/all invalid characters from before and after the JSON curly braces
		$patterns = array();

		$patterns[0] = '/courses\(/';
		$patterns[1] = '/\)/';
		$patterns[2] = '/;/';

		$replacements = array();

		$replacements[0] = '';

		$json = preg_replace( $patterns, $replacements, $http_body );

		$course_data = json_decode( $json );

	// iterate through json to output as nested html table
	if( ! empty( $course_data ) ) {
	
		// not happy with the lack of seperation of concerns here - needs work
		$html_output = '<table style="width: 100%" class="coursedata"><tbody>';
		$html_output .= '<tr class="tableheader"><th>Course ID</th><th>Start date</th><th>End date</th><th>Qualification</th></tr>';

		// list each course
		foreach( $course_data->courses as $course ) {
			$html_output .= '<tr class="header"><td><span>&#x25BA; </span>' . $course->id . '</td><td>' . $course->start. '</td><td>' . $course->end. '</td><td>' . $course->qualification . '</td></tr>';
			
			// list each student
			$html_output .= '<tr><td colspan="4" style="padding: 0;"><table style="width: 100%; border: 0; margin-bottom: 0;"><tbody>';
			$html_output .= '<tr class="subheader"><th>Student ID</th><th>Name</th></tr>';

		    foreach( $course->students as $student ) {
		    		if( ! empty( $student ) ) {
		            	$html_output .= '<tr class="student"><td>' . $student->id . '</td><td>' . $student->name . '</td></tr>';
				}
			

			}  

			$html_output .= '</tbody></table></td></tr>';
		}
			$html_output .= '</tbody></table>';

	}

    return $html_output;
}
 
function register_shortcodes() {
  add_shortcode( 'display_course_list', 'display_course_list' );
}