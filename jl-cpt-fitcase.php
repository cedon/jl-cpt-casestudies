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

require_once ( JLFITCASE__PLUGIN_DIR . '_inc/functions.php');
require_once ( JLFITCASE__PLUGIN_DIR . 'class-jl-custom-post-type.php' );

function jl_fitcase_flush_rewrite() {
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'jl_fitcase_flush_rewrite' );
register_deactivation_hook( __FILE__, 'jl_fitcase_flush_rewrite' );

$fitcase = new JL_CustomPostType( 'Case Study' );
$fitcase->add_taxonomy( 'Fitness Goal', array( 'hierarchical' => true ) );

$fitcase->add_meta_box(
	'Client Info',
	array(
		'First Name' => array(
			'type' => 'text',
			'maxlength' => 64,
		),
		'Last Name'  => array(
			'type'  => 'text',
			'maxlength'  => 64,
			'break' => true,
		),
		'Sex'        => array(
			'type'    => 'select',
			'options' => array( 'Male', 'Female', 'Other' ),
		),
		'Age'        => array(
			'type'  => 'text',
			'maxlength'  => 1,
			//'size' => 16,
			'break' => true,
		),
		'History' => array(
			'type'            => 'wpeditor',
			'editor_settings' => array(
				'media_buttons' => false,
				'textarea_rows' => 5,
			)
		),
	)
); // Client Info

$fitcase->add_meta_box(
	'Testing Checkboxes',
	array(
		'Checkbox One' => array(
			'type' => 'checkbox',
		),
		'Checkbox Two' => array(
			'type' => 'checkbox',
		),
		'Checkbox Three' => array(
			'type' => 'checkbox',
		),
	)
);

// Load Custom Admin Styles
function jl_fitcase_css() {
	global $post_type;
	if ( 'case_study' != $post_type ) {
		return;
	}
	wp_enqueue_style( 'jlfitcase-admin-style', plugins_url( '_css/jl-fitcase-admin.css', __FILE__ ) );
}
add_action( 'admin_enqueue_scripts', 'jl_fitcase_css' );
