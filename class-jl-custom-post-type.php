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

			// Set Variables
			$this->$post_type_name      = strtolower( str_replace( ' ','_', $name) );
			$this->$post_type_args      = $args;
			$this->$post_type_lables    = $labels;

			// Add Action to Register Custom Post Type if it Does Not Already Exist
			if( ! post_type_exists( $this->post_type_name) ) {
				add_action( 'init', array( &$this, 'register_post_type' ) );
			}

			// Listen for Save Post Hook
			$this->save();
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
