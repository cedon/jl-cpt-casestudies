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

$book = new JL_CustomPostType( 'Book' );
$book->add_taxonomy( 'Author' );

$book->add_meta_box(
	'Book Info',
	array(
		'Year' => 'text',
		'Genre' => 'text'
	)
);

$book->add_meta_box(
	'Author Info',
	array(
		'Name'        => 'text',
		'Nationality' => 'text',
	)
);