<?php
/**
 * Used to create a Custom Post Type
 *
 * @package jlfitcase
 * @since 1.0.0
 */

if ( ! class_exists( 'JL_Custom_Post_Type' ) ) {

	/**
	 * Class JL_Custom_Post_Type
	 */
	class JL_Custom_Post_Type {
		public $post_type_name;
		public $post_type_args;
		public $post_type_labels;

		/** Constructor */
		public function __construct( $name, $args = array(), $labels = array() ) {

		}

		/** Register post type method */
		public function register_post_type() {


		}

		/** Register taxonomy method */
		public function add_taxonomy( $name, $args = array(), $labels = array() ) {

		}

		/** Attaches meta boxes to the post type */
		public function add_meta_box( $title, $fields = array(), $context = 'normal', $priority = 'default' ) {

		}

		/** Listener for saving post */
		public function save() {


		}

	} // End Class.

}// End if().
