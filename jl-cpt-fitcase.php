<?php
/*
Plugin Name: JL Fitness Case Study Custom Post Type
Plugin URI: http://jonliebold.com
Description: A plugin to create a custom post type for Case Studies specifically for fitness professionals and facilities.
Version: 1.0
Author: Jon Liebold
Author URI: http://jonliebold.com
Text Domain: jlfitcase
License: GPLv2 or later
*/

define( 'JLFITCASE__VERSION', '1.0' );
define( 'JLFITCASE__PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'JLFITCASE__PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
define( 'JLFITCASE__PLUGIN_FILE', __FILE__ );

require_once( JLFITCASE__PLUGIN_DIR . 'class-jl-custom-post-type.php' );

$fitcase = new JL_CustomPostType( 'Case Study' );
$fitcase->add_taxonomy( 'Type' );

$fitcase->add_meta_box(
	'Client Info',
	array(
		'Client First Name' => array(
			'type' => 'text',
			'size' => 128,
		),
		'Sex'               => array(
			'type' => 'select',
			'options' => array( 'Male', 'Female', 'Other' ),
		),
	)
);

// Add Class to Post Meta Boxes


// Load Custom Admin Styles
function jl_fitcase_css() {
	global $post_type;
	if ( 'case_study' != $post_type ) {
		return;
	}
	wp_enqueue_style( 'jlfitcase-admin-style', plugins_url( '_css/jl-fitcase-admin.css', __FILE__ ) );
}
add_action( 'admin_enqueue_scripts', 'jl_fitcase_css' );
