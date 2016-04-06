<?php
/*
Plugin Name: Advanced Custom Fields: Font Awesome
Description: Add a Font Awesome field type to Advanced Custom Fields
Version: 1.7.2
Author: Matt Keys
Author URI: http://mattkeys.me/
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

function register_fields_font_awesome() {

	if ( ! class_exists( 'acf' ) ) {
		return;
	}

	global $acf;

	if ( version_compare( $acf->settings['version'], '5.0', '>=' ) ) {
		include_once( 'acf-font-awesome-v5.php' );
	} else {
		include_once( 'acf-font-awesome-v4.php' );
	}
	
}
add_action( 'init', 'register_fields_font_awesome' );
