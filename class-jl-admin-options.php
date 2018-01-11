<?php
/**
 * Used to create a custom admin settings pages, groups, and callback functions.
 *
 * @package jlfitcase
 * @since 1.0.0
 */

if ( ! class_exists( 'JL_CustomAdminOptions' ) ) {

	/**
	 * The JL Custom Admin Options generator class
	 *
	 * @since 1.0.0
	 */
	class JL_CustomAdminOptions {

		/**
		 * The name of the menu
		 *
		 * @since 1.0.0
		 * @access public
		 * @var string
		 */
		public $admin_menu_name;

		/**
		 * Arguments for creation of the admin menu page
		 *
		 * @since 1.0.0
		 * @access public
		 * @var array
		 */
		public $admin_menu_args;

		/**
		 * Constructor
		 *
		 * @param string $name The name of the menu
		 * @param array $args (optional) Arguments for creation of the menu page
		 * @param bool $submenu (optional) Flag for submenu creation versus main admin page
		 */
		public function __construct( $name, $args = array(), $submenu = false ) {

			// Set Variables
			$this->admin_menu_name = self::beautify( $name );
			$this->admin_menu_args = $args;

			$args = array_merge(

				// Default Values
				array(
					'page_title'  => $this->admin_menu_name,
					'menu_title'  => '',
					'capability'  => 'administrator',
					'menu_slug'   => self::uglify( $this->admin_menu_name ),
					'icon_url'    => '',
					'position'    => 5,
					'parent_slug' => '',
				),

				// Add in user overrides
				$this->admin_menu_args
			);

			if ( $submenu === false ) {

			} elseif ( $submenu === true ) {

			} else {
				// Trap the Error
			}
		}


		/**
		 * Changes a string like 'my_string' to 'My String' for display purposes
		 *
		 * @since 1.0
		 * @access public
		 *
		 * @param string $string The string of text to beautify
		 *
		 * @return string The beautified text string
		 */
		public static function beautify( $string ) {
			return ucwords( str_replace( '_', ' ', $string ) );
		}

		/**
		 * Changes a string like 'My String' to 'my_string' for display purposes
		 *
		 * @since 1.0
		 * @access public
		 *
		 * @param string $string The string of text to uglify
		 *
		 * @return string The uglified text string
		 */
		public static function uglify( $string ) {
			return strtolower( str_replace( ' ', '_', $string ) );
		}

	} // End Class

} // End if()