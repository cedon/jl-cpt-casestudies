<?php
/**
 * Used to create a Custom Post Type
 *
 * @package jlfitcase
 * @since 1.0.0
 */

if ( ! class_exists( 'JL_CustomPostType' ) ) {

	/**
	 * The JL Custom Post Type Generator Class
	 *
	 * @since 1.0.0
	 */
	class JL_CustomPostType {

		/**
		 *
		 * Name of Custom Post Type set by the user
		 *
		 * @since 1.0.0
		 * @access public
		 * @var string
		 */
		public $post_type_name;

		/**
		 *
		 * Optional key used for custom post type. This will be used to preface names, IDs, etc. Will default to a
		 * eight character truncation of $post_type_name but can be set using the set_post_key() method.
		 *
		 * @since 1.0.0
		 * @access public
		 * @var string
		 */
		public $post_type_key;

		/**
		 * Arguments for custom post type registration
		 *
		 * @since 1.0.0
		 * @access public
		 * @var array
		 */
		public $post_type_args;

		/**
		 * Labels for custom post type
		 *
		 * @since 1.0.0
		 * @access public
		 * @var array
		 */
		public $post_type_labels;

		/**
		 * Array of upload locations for attachments
		 *
		 * @since 1.0.0
		 * @access public
		 * @var array
		 */
		public $post_upload_loc;

		/**
		 * Constructor
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @param string $name Name of the custom post type
		 * @param array $args (optional) Array of argument overrides
		 * @param array $labels (optional) Array of label overrides
		 */
		public function __construct( $name, $args = array(), $labels = array() ) {

			// Set Variables
			$this->post_type_name      = self::uglify( $name );
			$this->post_type_key       = substr( preg_replace( "/[^a-z]+/", "", self::uglify( $name ) ), 0, 8 );
			$this->post_type_args      = $args;
			$this->post_type_labels    = $labels;

			// Add Action to Register Custom Post Type if it Does Not Already Exist
			if( ! post_type_exists( $this->post_type_name) ) {
				add_action( 'init', array( &$this, 'register_post_type' ) );
			}

			// Set upload locations
			$this->set_post_upload_loc();

			// Create custom upload location
			if ( ! wp_mkdir_p( $this->post_upload_loc['path'] ) ) {
				wp_mkdir_p( $this->post_upload_loc['path'] );
			}
			chmod( $this->post_upload_loc['path'], 0755 );

			// Listen for Save Post Hook
			$this->save();

		}

		/**
		 * Register post type method
		 *
		 * @since 1.0.0
		 * @access public
		 */
		public function register_post_type() {

			// Capitalize words and make them plural
			$name   = self::beautify( $this->post_type_name );
			$plural = self::pluralize( $name );

			// Set labels with some defaults and merge in overrides
			$labels = array_merge(

				// Default values
				array(
					'name'               => _x( $plural, 'Post Type General Name', 'jlfitcase' ),
					'singular_name'      => _x( $name, 'Post Type Singular Name', 'jlfitcase' ),
					'add_new'            => _x( 'Add New ', strtolower( $name ), 'jlfitcase' ),
					'add_new_item'       => __( 'Add New ' . $name, 'jlfitcase' ),
					'edit_item'          => __( 'Edit ' . $name, 'jlfitcase' ),
					'new_item'           => __( 'New ' . $name, 'jlfitcase' ),
					'all_items'          => __( 'All ' . $plural, 'jlfitcase' ),
					'view_item'          => __( 'View ' . $name, 'jlfitcase' ),
					'search_items'       => __( 'Search ', $plural, 'jlfitcase' ),
					'not_found'          => __( 'No ' . strtolower( $plural ) . ' found', 'jlfitcase' ),
					'not_found_in_trash' => __( 'No ' . strtolower( $plural ) . ' found in Trash', 'jlfitcase' ),
					'parent_item_colon'  => '',
					'menu_name'          => $plural
				),

				// Overrides
				$this->post_type_labels
			);

			// Set default arguments and merge in overrides
			$args = array_merge(

				// Default Values
				array(
					'label'             => $plural,
					'labels'            => $labels,
					'public'            => true,
					'show_ui'           => true,
					'supports'          => array( 'title', 'editor' ),
					'show_in_nav_menus' => true,
					'_builtin'          => false,
					'show_in_rest'      => true,
				),

				// Overrides
				$this->post_type_args
			);

			// Create the Custom Post Type
			register_post_type( $this->post_type_name, $args );

		}

		/**
		 * Register taxonomy method
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @param string $name Name of the custom taxonomy
		 * @param array $args (optional) Array of argument overrides for taxonomy creation
		 * @param array $labels (optional) Array of label overrides for taxonomy
		 */
		public function add_taxonomy( $name, $args = array(), $labels = array() ) {

			if( ! empty( $name ) ) {

				// Get Post Name
				$post_type_name = $this->post_type_name;

				// Taxonomy Properties
				$taxonomy_name   = self::uglify( $name );
				$taxonomy_labels = $labels;
				$taxonomy_args   = $args;

				if( ! taxonomy_exists( $taxonomy_name ) ) {

					// Capitalize words and make them plural
					$name   = self::beautify( $name );
					$plural = self::pluralize( $name );

					// Set labels with some defaults and merge in overrides
					$labels = array_merge(

						// Defaults
						array(
							'name'               => _x( $plural, 'Post Type General Name', 'jlfitcase' ),
							'singular_name'      => _x( $name, 'Post Type Singular Name', 'jlfitcase' ),
							'search_items'       => __( 'Search ' . $plural, 'jlfitcase' ),
							'parent_item'        => __( 'Parent ' . $name, 'jlfitcase' ),
							'parent_item_colon'  => __( 'Parent ' . $name . ':', 'jlfitcase' ),
							'edit_item'          => __( 'Edit ' . $name, 'jlfitcase' ),
							'update_item'        => __( 'Update ' . $name, 'jlfitcase' ),
							'add_new_item'       => __( 'Add New ' . $name, 'jlfitcase' ),
							'new_item_name'      => __( 'New ' . $name . ' Name', 'jlfitcase' ),
							'menu_name'          => __( $name ),
						),

						// Overrides
						$taxonomy_labels
					);

					// Set default arguments and merge in overrides
					$args = array_merge(

					// Default Values
						array(
							'label'             => $plural,
							'labels'            => $labels,
							'public'            => true,
							'show_ui'           => true,
							'show_in_nav_menus' => true,
							'_builtin'          => false,
						),

						// Overrides
						$taxonomy_args
					);

					// Create Taxonomy and Add it to the Post Type
					add_action( 'init',
						function() use( $taxonomy_name, $post_type_name, $args ) {
							register_taxonomy( $taxonomy_name, $post_type_name, $args );
						}
					);

				} else {

					// Add Already-Existing Taxonomy to Post Type
					add_action( 'init',
						function() use( $taxonomy_name, $post_type_name ) {
							register_taxonomy_for_object_type( $taxonomy_name, $post_type_name );
						}
					);
				}
			}
		}

		/**
		 * Attaches meta boxes to the post type
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @param string $title Title of meta box
		 * @param array $fields (optional) Array of fields to add to meta box
		 * @param string $description (optional) Text description of the meta box displayed to users
		 * @param string $context (optional) Context on the screen where the meta box should display
		 * @param string $priority (optional) The priority within the context where the meta box should display
		 */
		public function add_meta_box( $title, $fields = array(), $description ='', $context = 'normal', $priority =
		'default' ) {

			if ( ! empty( $title ) ) {

				// Get Post Type Name
				$post_type_name = $this->post_type_name;

				// Metabox Variables
				$box_id       = self::uglify( $title );
				$box_title    = self::beautify( $title );
				$box_context  = $context;
				$box_priority = $priority;
				$box_description = $description;

				// Make fields global
				global $custom_fields;
				$custom_fields[$title] = $fields;

				add_action( 'add_meta_boxes',
					function() use( $box_id, $box_title, $post_type_name, $box_context, $box_priority, $fields,
						$box_description ) {

						// Create Callback Arguments Array w/ $fields
						$callback_args = array( $fields );

						// Check for Description
						if ( isset( $box_description ) && $box_description != '' ) {
							$callback_args['description'] = $box_description;
						}

						// Check for wp_editor() fields and set flag to use classic editor
						foreach ( $fields as $field ) {
							if ( in_array( 'wpeditor', $field ) ) {
								$callback_args['__block_editor_compatible_meta_box'] = false;
							}
						}

						add_meta_box(
							$box_id,
							$box_title,
							function( $post, $data ) {
								global $post;

								// Nonce Field for Validation
								$nonce_field = $this->post_type_key . '-nonce';
								wp_nonce_field( JLFITCASE__PLUGIN_FILE, $nonce_field );

								// Display Description if one is set
								if ( isset( $data['args']['description'] ) ) {
									echo '<p class="box-desc">' . $data['args']['description'] . '</p>' .
									     PHP_EOL;
								}

								// Get Inputs from $data
								$custom_fields = $data['args'][0];

								// Get Saved Values
								$meta = get_post_custom( $post->ID );

								// Check Array and Loop
								if ( ! empty( $custom_fields ) ) {
									foreach ( $custom_fields as $label => $field ) {
										$field_id_name = self::uglify( $data['id'] ) . '_' . self::uglify( $label );
										$field_type    = self::uglify( $field['type'] );

										// Check for attributes in the field
										if ( isset( $field['attributes'] ) && ! empty ( $field['attributes'] ) ) {
											$attributes = $field['attributes'];
										} else {
											$attributes = array();
										}

										// Check for select options in the field definition
										if ( isset( $field['select_options'] ) && ! empty ( $field['select_options'] ) ) {
											$select_options = $field['select_options'];
										} else {
											$select_options = array();
										}

										// Check for radio options in the field definition
										if ( isset( $field['radio_options'] ) && ! empty ( $field['radio_options'] ) ) {
											$radio_options = $field['radio_options'];
										} else {
											$radio_options = array();
										}

										// Check for wp_editor() options in field definition
										if ( isset( $field['wpeditor_options'] ) && ! empty( $field['wpeditor_options'] ) ) {
											$wpeditor_settings = $field['wpeditor_options'];
										} else {
											$wpeditor_settings = array();
										}

										echo '<div class="jlfitcase-meta-' . $field_type . '">';
										echo self::add_input_label( $field_id_name, $label );
										echo self::add_meta_field( $field_id_name, $field_type, $meta,
										$attributes,
											$select_options, $radio_options, $wpeditor_settings );
										echo '</div>';

										if ( isset ( $field['break'] ) && $field['break'] == true ) {
											echo '<br />';
										}
									}
								}

							},
							$post_type_name,
							$box_context,
							$box_priority,
							$callback_args
						); // add_meta_box()

						// Add dynamic filter using function add_meta_box_class() located in functions.php
						$filter_name = 'postbox_classes_' . $post_type_name . '_' . $box_id;
						add_filter( $filter_name , 'add_meta_box_class' );
					}
				);
			}
		}

		/** Listener for saving post */
		public function save() {
			$post_type_name = $this->post_type_name;

			add_action( 'save_post',
				function () use ( $post_type_name ) {

					// Do not autosave meta box data
					if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
						return;
					}

					// Abort if the nonce field is not set
					$nonce_field = $this->post_type_key . '-nonce';
					if ( ! isset( $_POST[$nonce_field] ) ||
					     ! wp_verify_nonce( $_POST[$nonce_field], JLFITCASE__PLUGIN_FILE ) ) {
						return;
					}

					global $post;

					if ( isset( $_POST ) && isset( $post->ID ) && get_post_type( $post->ID ) == $post_type_name ) {
						global $custom_fields;

						// Loop through all meta boxes
						foreach ( $custom_fields as $title => $fields ) {

							// Loop through all fields in meta box
							foreach ( $fields as $label => $type ) {

								$field_name = self::uglify( $title ) . '_' . self::uglify( $label );

								// Prevent PHP Warnings for undefined index
								if ( isset( $_POST[$this->post_type_key][ $field_name ] ) ) {
									$metadata = $_POST[$this->post_type_key][ $field_name ];
								} else {
									$metadata = null;
								}

								if ( ! empty( $_FILES[$field_name]['name'] ) ) {
									require_once( ABSPATH . 'wp-admin/includes/file.php' );
									$override['action'] = 'editpost';

									$file_name = $_FILES[$field_name]['name'];
									$attachment_file = wp_handle_upload( $_FILES[$field_name], $override );
									$post_id = $post->ID;


									$attachment = array(
										'post_title'     => $file_name,
										'post_content'   => '',
										'post_type'      => 'attachment',
										'post_parent'    => $post_id,
										'post_mime_type' => $_FILES[$field_name]['type'],
										'guid'           => $attachment_file['url'],
									);


									// Check if file is a JPEG or PNG and require wp-admin/includes/image.php
									if ( $_FILES[$field_name]['type'] == 'image/jpeg' || $_FILES[$field_name]['type']
									                                                     == 'image/png' ) {
										require_once( ABSPATH . 'wp-admin/includes/image.php' );

										// Temporarily Disable All Defined Image Sizes & Change Upload Directory
										add_filter( 'intermediate_image_sizes_advanced', 'fitcase_remove_image_sizes', 10, 2 );

									}

									$id = wp_insert_attachment( $attachment, $attachment_file['file'], $post_id );


									wp_update_attachment_metadata( $id, wp_generate_attachment_metadata( $id,
										$attachment_file['file'] ) );

									// Set metadata value to save to database
									$metadata = $attachment_file['url'];

									if ( $_FILES[$field_name]['type'] == 'image/jpeg' || $_FILES[$field_name]['type']
									                                                     == 'image/png' ) {
										remove_filter( 'intermediate_image_sizes_advanced', 'fitcase_remove_image_sizes' );
									}
								}

								if ( $metadata != null ) {
									update_post_meta( $post->ID, $field_name, $metadata );
								}
							}
						}
					}
				}
			);

		}

		/**
	 * Redefines $this->post_type_key with user-selected override
	 *
	 * @since 1.0.0.
	 * @access public
	 *
	 * @param $key (string) The key the user wishes to use as an override
	 */
		public function set_post_key( $key ) {
			$newkey = preg_replace( "/[^a-z]+/", "", self::uglify( $key ) );
			$this->post_type_key = $newkey;
		}

		/**
		 * Sets the value of $this->post_type_upload_loc with a custom folder located in ./wp-content/uploads
		 *
		 * @since 1.0.0.
		 * @access public
		 */
		public function set_post_upload_loc() {
			$upload_subdir = '/' . self::uglify( $this->post_type_name );
			$upload_array = wp_upload_dir();

			$upload_array['subdir'] = $upload_subdir;
			$upload_array['path'] = $upload_array['basedir'] . $upload_array['subdir'];
			$upload_array['url'] = $upload_array['baseurl'] . $upload_array['subdir'];

			$this->post_upload_loc = $upload_array;
		}

		/**
		 * Builds an HTML <label> element for an <input> element
		 *
		 * @since 1.0.0.
		 * @access public
		 *
		 * @param $input_id (string) The value of the id attribute of the <input> element
		 * @param $label (string) The text that will be shown inside the <label> element
		 *
		 * @return string The HTML <label> element
		 */
		public static function add_input_label( $input_id, $label ) {
			return '<label for"' . $input_id . '" >' . self::beautify( $label ) . '</label>';
		}

		/**
		 * Builds the attributes for an HTML <input> element based on an array
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @param array $attributes (array) An array of valid HTML attributes
		 *
		 * @return string The attribute string for the <input> element
		 */
		public static function input_attributes( $attributes ) {
			// Initialize HTML attributes to be returned
			$input_attributes = '';

			// Arrays for validation
			$num_attribs  = array( 'maxlength', 'min', 'size', 'rows', 'cols' );
			$bool_attribs = array( 'required' );

			foreach ( $attributes as $attribute => $value ) {
				// Make sure the attribute is all lower-case
				$attribute = strtolower( $attribute );

				if ( in_array( $attribute, $num_attribs ) and is_int( $value ) ) {
					$input_attributes .= $attribute . '="' . $value . '" ';
				} elseif ( in_array( $attribute, $bool_attribs ) and is_bool( $value ) ) {
					$eval              = ( $value ) ? 'true' : 'false';
					$input_attributes .= $attribute . '="' . $eval . '" ';
				}
			}

			return $input_attributes;

		}

		/**
		 * Creates a field for use in a custom post type meta box.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @param string $field_id_name The id attribute of the form element
		 * @param string $field_type The type of form element to be created
		 * @param array $meta An array of meta values retrieved from the database if they exist
		 * @param array $attributes An array of HTML attributes and values to be used in the  meta field form element
		 * @param array $select_options An array of labels to be used on option elements for a select element
		 * @param array $radio_options An array of labels to be used to create radio buttons within a group
		 * @param array $wpeditor_options An array of settings to be passed for an instance of wp_editor()
		 *
		 * @return string The custom meta as HTML form element
		 */
		function add_meta_field(
			$field_id_name, $field_type, $meta, $attributes, $select_options,
			$radio_options,
			$wpeditor_options
		) {

			// Set key
			$post_key = $this->post_type_key;

			// Initialize Meta Field
			$meta_field = '';

			// Check for meta data for value attribute and set to null if not found
			if ( ! isset( $meta[$field_id_name] ) ) {
				$meta[$field_id_name][0] = null;
			}

			// Text Fields
			if ( $field_type == 'text' ) {
				$meta_field .= '<input type="' . $field_type . '" name="'. "{$post_key}" . '[' . $field_id_name . ']" id="' .
				                $field_id_name . '" value="' . $meta[$field_id_name][0] . '" ';

				if ( isset( $attributes ) && ! empty( $attributes ) ) {
					$meta_field .= self::input_attributes( $attributes );
				}

				$meta_field .= ' />';
			}

			// Select Elements
			if ( $field_type == 'select' ) {
				$meta_field .= '<select name="'. "{$post_key}" . '[' . $field_id_name . ']" id="' .
				               $field_id_name . '">';

				foreach ( $select_options as $option ) {
					$meta_field .= '<option value="' . $option . '" ' . selected( $meta[$field_id_name][0],
							$option, false ) . ' >' .
					                 $option . '</option>';
				}

				$meta_field .= '</select>';
			}

			// Check Boxes
			if ( $field_type == 'checkbox' ) {
				$meta_field .= '<input type="' . $field_type . '" name="'. "{$post_key}" . '[' . $field_id_name . ']" id="' . $field_id_name . '" value="' . $field_id_name . '" ' . checked( $meta[ $field_id_name ][0], $field_id_name, false ) . ' />';
			}

			// Radio Buttons
			if ( $field_type == 'radio' ) {
				foreach ( $radio_options as $radio ) {
					$meta_field .= '<input type="' . $field_type . '" name="'. "{$post_key}" . '[' . $field_id_name . ']" id="' . $field_id_name . '" value="' . $radio . '" ' . checked( $meta[ $field_id_name ][0], $radio, false ) . ' />';
					$meta_field .= self::add_input_label( $field_id_name, $radio );
				}
			}

			// Text Area
			if ( $field_type == 'textarea' ) {
				$meta_field .= '<textarea name="'. "{$post_key}" . '[' . $field_id_name . ']" id="' . $field_id_name . '" ';

				if ( isset( $attributes ) && ! empty( $attributes ) ) {
					$meta_field .= self::input_attributes( $attributes );
				}

				$meta_field .= ' />';

				if ( isset( $meta[ $field_id_name ] ) ) {
					$meta_field .=  $meta[ $field_id_name ][0];
				}

				$meta_field .= '</textarea>';
			}

			// wp_editor() Instance
			if ( $field_type == 'wpeditor' ) {
				if ( isset( $meta[ $field_id_name ] ) ) {
					$editor_content = $meta[ $field_id_name ][0];
				} else {
					$editor_content = '';
				}

				$wpeditor_options['textarea_name'] = "{$this->post_type_key}" . '[' . $field_id_name . ']';

				wp_editor( $editor_content, $field_id_name, $wpeditor_options );
			}

			// File Upload Field
			if ( $field_type == 'attachment' ) {
				$meta_field .= '<input type="file" name="' . $field_id_name . '" id="' .
				               $field_id_name . '" value="' . $meta[$field_id_name][0] . '" size="25">';

				if ( isset( $meta[$field_id_name][0] ) ) {
					$attachment_id = attachment_url_to_postid( $meta[$field_id_name][0] );
					$upload_meta = wp_get_attachment_metadata( $attachment_id );


					if ( isset( $upload_meta['image_meta'] ) ) {
						$img_max_width = 300;
						if ( $upload_meta['width'] > $img_max_width ) {
							$img_ratio = $upload_meta['width'] / $img_max_width;
							$img_width = $img_max_width;
							$img_height = $upload_meta['height'] / $img_ratio;
						} else {
							$upload_meta['width'];
							$img_height = $upload_meta['height'];
						}

						$meta_field .= '<br /> <img src="' . $meta[$field_id_name][0] . '" width="' . $img_width . '" height="' . $img_height .
						     '">';
					}
				}
			}

			// Return Completed Meta Field
			return $meta_field;
		}

		/**
		 * Returns the post_type_name of the object for use elsewhere
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @return string The value of $this->post_type_name
		 */
		function getPostTypeName() {
			return $this->post_type_name;
		}

	/**
	 * Returns the beautified Post Type of the object for use elsewhere
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string The value of $this->post_type_name
	 */
		function getPostType() {
			return self::beautify( $this->post_type_name );
		}

		/**
		 * Returns the upload location array
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @return array The value of $this->post_upload_loc
		 */
		function getPostUploadLoc() {
			return $this->post_upload_loc;
		}

		/**
		 * Changes a string like 'my_string' to 'My String' for display purposes
		 *
		 * @since 1.0.0
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
		 * @since 1.0.0
		 * @access public
		 *
		 * @param string $string The string of text to uglify
		 *
		 * @return string The uglified text string
		 */
		public static function uglify( $string ) {
			return strtolower( str_replace( ' ', '_', $string ) );
		}

		/**
		 * Converts a provided word into its plural form
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @param string $string The word to be pluralized
		 *
		 * @return string The pluralized form of $string
		 */
		public static function pluralize( $string ) {
			$exceptions = array(
				'Amoyese',
				'bison',
				'Borghese',
				'bream',
				'breeches',
				'britches',
				'buffalo',
				'cantus',
				'carp',
				'chassis',
				'clippers',
				'cod',
				'coitus',
				'Congoese',
				'contretemps',
				'corps',
				'debris',
				'diabetes',
				'djinn',
				'eland',
				'elk',
				'equipment',
				'Faroese',
				'flounder',
				'Foochowese',
				'Furniture',
				'gallows',
				'Genevese',
				'Genoese',
				'Gilbertese',
				'graffiti',
				'headquarters',
				'herpes',
				'hijinks',
				'Hottentotese',
				'information',
				'innings',
				'jackanapes',
				'Kiplingese',
				'Kongoese',
				'Lucchese',
				'Luggage',
				'mackerel',
				'Maltese',
				'.*?media',
				'mews',
				'moose',
				'mumps',
				'Nankingese',
				'news',
				'nexus',
				'Niasese',
				'Pekingese',
				'Piedmontese',
				'pincers',
				'Pistoiese',
				'pliers',
				'Portuguese',
				'proceedings',
				'rabies',
				'rice',
				'rhinoceros',
				'salmon',
				'Sarawakese',
				'scissors',
				'sea[- ]bass',
				'series',
				'Shavese',
				'shears',
				'siemens',
				'species',
				'staff',
				'swine',
				'testes',
				'trousers',
				'trout',
				'tuna',
				'Vermontese',
				'Wenchowese',
				'whiting',
				'wildebeest',
				'Yengeese',
			);


			$rules = array(
				'/(s)tatus$/i'                                                           => '\1\2tatuses',
				'/(quiz)$/i'                                                             => '\1zes',
				'/^(ox)$/i'                                                              => '\1\2en',
				'/([m|l])ouse$/i'                                                        => '\1ice',
				'/(matr|vert|ind)(ix|ex)$/i'                                             => '\1ices',
				'/(x|ch|ss|sh)$/i'                                                       => '\1es',
				'/([^aeiouy]|qu)y$/i'                                                    => '\1ies',
				'/(hive|gulf)$/i'                                                        => '\1s',
				'/(?:([^f])fe|([lr])f)$/i'                                               => '\1\2ves',
				'/sis$/i'                                                                => 'ses',
				'/([ti])um$/i'                                                           => '\1a',
				'/(p)erson$/i'                                                           => '\1eople',
				'/(m)an$/i'                                                              => '\1en',
				'/(c)hild$/i'                                                            => '\1hildren',
				'/(f)oot$/i'                                                             => '\1eet',
				'/(buffal|her|potat|tomat|volcan)o$/i'                                   => '\1\2oes',
				'/(alumn|bacill|cact|foc|fung|nucle|radi|stimul|syllab|termin|vir)us$/i' => '\1i',
				'/us$/i'                                                                 => 'uses',
				'/(alias)$/i'                                                            => '\1es',
				'/(analys|ax|cris|test|thes)is$/i'                                       => '\1es',
				'/s$/'                                                                   => 's',
				'/^$/'                                                                   => '',
				'/$/'                                                                    => 's',
			);

			$uninflected = array(
				'/.*[nrlm]ese/',
				'/.*deer/',
				'/.*fish/',
				'/.*measles/',
				'/.*ois/',
				'/.*pox/',
				'/.*sheep/',
				'/people/',
				'/cookie/',
				'/police/',
			);

			$irregular = array(
				'atlas'        => 'atlases',
				'axe'          => 'axes',
				'beef'         => 'beefs',
				'brother'      => 'brothers',
				'cafe'         => 'cafes',
				'chateau'      => 'chateaux',
				'niveau'       => 'niveaux',
				'child'        => 'children',
				'cookie'       => 'cookies',
				'corpus'       => 'corpuses',
				'cow'          => 'cows',
				'criterion'    => 'criteria',
				'curriculum'   => 'curricula',
				'demo'         => 'demos',
				'domino'       => 'dominoes',
				'echo'         => 'echoes',
				'foot'         => 'feet',
				'fungus'       => 'fungi',
				'ganglion'     => 'ganglions',
				'genie'        => 'genies',
				'genus'        => 'genera',
				'graffito'     => 'graffiti',
				'hippopotamus' => 'hippopotami',
				'hoof'         => 'hoofs',
				'human'        => 'humans',
				'iris'         => 'irises',
				'larva'        => 'larvae',
				'leaf'         => 'leaves',
				'loaf'         => 'loaves',
				'man'          => 'men',
				'medium'       => 'media',
				'memorandum'   => 'memoranda',
				'money'        => 'monies',
				'mongoose'     => 'mongooses',
				'motto'        => 'mottoes',
				'move'         => 'moves',
				'mythos'       => 'mythoi',
				'niche'        => 'niches',
				'nucleus'      => 'nuclei',
				'numen'        => 'numina',
				'occiput'      => 'occiputs',
				'octopus'      => 'octopuses',
				'opus'         => 'opuses',
				'ox'           => 'oxen',
				'passerby'     => 'passersby',
				'penis'        => 'penises',
				'person'       => 'people',
				'plateau'      => 'plateaux',
				'runner-up'    => 'runners-up',
				'sex'          => 'sexes',
				'soliloquy'    => 'soliloquies',
				'son-in-law'   => 'sons-in-law',
				'syllabus'     => 'syllabi',
				'testis'       => 'testes',
				'thief'        => 'thieves',
				'tooth'        => 'teeth',
				'tornado'      => 'tornadoes',
				'trilby'       => 'trilbys',
				'turf'         => 'turfs',
				'volcano'      => 'volcanoes',
			);

			foreach ( $uninflected as $pattern ) {
				if ( preg_match( $pattern, strtolower( $string ) ) ) {
					$output = $string;

					return $output;
				}
			}

			if ( in_array( $string, $exceptions ) ) {
				$output = $string;

				return $output;
			} elseif ( array_key_exists( $string, $irregular ) ) {
				$output = $irregular[ $string ];

				return $output;
			} else {
				foreach ( $rules as $rule => $pattern ) {
					if ( preg_match( $rule, $string ) ) {
						$output = preg_replace( $rule, $pattern, $string );

						return $output;
						break;
					}

				}
			}
		}

	} // End Class.

}// End if().
