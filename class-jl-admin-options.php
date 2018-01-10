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
		 * The name of the menu that will be used within the page's title element
		 *
		 * @since 1.0.0
		 * @access public
		 * @var string
		 */
		public $admin_page_title;

		/**
		 * The name of the menu that will appear in the admin menu
		 *
		 * @since 1.0.0
		 * @access public
		 * @var string
		 */
		public $admin_menu_title;

		public function __construct( $title, $menu ) {

			// Set Variables
			$this->admin_page_title = self::beautify( $title );
			$this->admin_menu_title = self::beautify( $menu );
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