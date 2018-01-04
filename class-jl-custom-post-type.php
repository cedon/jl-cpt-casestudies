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
	 * @since 1.0
	 */
	class JL_CustomPostType {

		/**
		 *
		 * Name of Custom Post Type set by the user
		 *
		 * @since 1.0
		 * @access public
		 * @var string
		 */
		public $post_type_name;

		/**
		 * Arguments for custom post type registration
		 *
		 * @since 1.0
		 * @access public
		 * @var array
		 */
		public $post_type_args;

		/**
		 * Labels for custom post type
		 *
		 * @since 1.0
		 * @access public
		 * @var array
		 */
		public $post_type_labels;

		/**
		 * Constructor
		 *
		 * @since 1.0
		 * @access public
		 *
		 * @param string $name Name of the custom post type
		 * @param array $args (optional) Array of argument overrides
		 * @param array $labels (optional) Array of label overrides
		 */
		public function __construct( $name, $args = array(), $labels = array() ) {

			// Set Variables
			$this->post_type_name      = self::uglify( $name );
			$this->post_type_args      = $args;
			$this->post_type_labels    = $labels;

			// Add Action to Register Custom Post Type if it Does Not Already Exist
			if( ! post_type_exists( $this->post_type_name) ) {
				add_action( 'init', array( &$this, 'register_post_type' ) );
			}

			// Listen for Save Post Hook
			$this->save();
		}

		/**
		 * Register post type method
		 *
		 * @since 1.0
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
		 * @since 1.0
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
		 * @since 1.0
		 * @access public
		 *
		 * @param string $title Title of meta box
		 * @param array $fields (optional) Array of fields to add to meta box
		 * @param string $context (optional) Context on the screen where the meta box should display
		 * @param string $priority (optional) The priority within the context where the meta box should display
		 */
		public function add_meta_box( $title, $fields = array(), $context = 'normal', $priority = 'default' ) {

			if ( ! empty( $title ) ) {

				// Get Post Type Name
				$post_type_name = $this->post_type_name;

				// Metabox Variables
				$box_id       = self::uglify( $title );
				$box_title    = self::beautify( $title );
				$box_context  = $context;
				$box_priority = $priority;

				// Make fields global
				global $custom_fields;
				$custom_fields[$title] = $fields;

				add_action( 'add_meta_boxes',
					function() use( $box_id, $box_title, $post_type_name, $box_context, $box_priority, $fields ) {

						// Create Callback Arguments Array w/ $fields
						$callback_args = array( $fields );

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
								wp_nonce_field( JLFITCASE__PLUGIN_FILE, 'jl-fitcase-nonce' );

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

										echo self::add_input_label( $field_id_name, $label );
										echo self::add_meta_field( $field_id_name, $field_type, $meta,
										$attributes,
											$select_options, $radio_options, $wpeditor_settings );

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
					if ( ! wp_verify_nonce( $_POST['jl-fitcase-nonce'], JLFITCASE__PLUGIN_FILE ) ) {
						return;
					}

					global $post;

					if ( isset( $_POST ) && isset( $post->ID ) && get_post_type( $post->ID ) == $post_type_name ) {
						global $custom_fields;

						error_log( print_r( $_POST, true) );
						// Loop through all meta boxes
						foreach ( $custom_fields as $title => $fields ) {

							//error_log( 'Title: '. $title . PHP_EOL );

							// Loop through all fields in meta box
							foreach ( $fields as $label => $type ) {
								// error_log( 'Label: '. $label . PHP_EOL );

								$field_name = self::uglify( $title ) . '_' . self::uglify( $label );

								// Prevent PHP Warnings for undefined index
								if ( isset( $_POST['fitcase'][ $field_name ] ) ) {
									$metadata = $_POST['fitcase'][ $field_name ];
								} else {
									$metadata = null;
								}
								update_post_meta( $post->ID, $field_name, $metadata );
							}
						}
					}
				}
			);

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
			$num_attribs  = array( 'maxlength', 'min', 'size' );
			$bool_attribs = array( 'required' );

			foreach ( $attributes as $attribute => $value ) {
				// Make sure the attribute is all lower-case
				$attribute = strtolower( $attribute );

				if ( in_array( $attribute, $num_attribs ) and is_int( $value ) ) {
					$input_attributes .= $attribute . '="' . $value . '" ';
				} elseif ( in_array( $attribute, $bool_attribs ) and is_bool( $value ) ) {
					$eval            = ( $value ) ? 'true' : 'false';
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

			// Initialize Meta Field
			$meta_field = '';

			// Text Fields
			if ( $field_type == 'text' ) {
				$meta_field .= '<input type="' . $field_type . '" name="fitcase[' . $field_id_name . ']" id="' .
				                $field_id_name . '" value="' . $meta[ $field_id_name ][0] . '" ';

				if ( isset( $attributes ) && ! empty( $attributes ) ) {
					$meta_field .= self::input_attributes( $attributes );
				}

				$meta_field .= ' />';
			}

			// Select Elements
			if ( $field_type == 'select' ) {
				$meta_field .= '<select name="fitcase[' . $field_id_name . ']" id="' .
				               $field_id_name . '">';

				foreach ( $select_options as $option ) {
					$meta_field .= '<option value="' . $option . '" ' . selected( $meta[ $field_id_name ][0],
							$option, false ) . ' >' .
					                 $option . '</option>';
				}

				$meta_field .= '</select>';
			}

			// Check Boxes
			if ( $field_type == 'checkbox' ) {
				$meta_field .= '<input type="' . $field_type . '" name="fitcase[' . $field_id_name . ']" id="' . $field_id_name . '" value="' . $field_id_name . '" ' . checked( $meta[ $field_id_name ][0], $field_id_name, false ) . ' />';
			}

			// Radio Buttons
			if ( $field_type == 'radio' ) {
				foreach ( $radio_options as $radio ) {
					$meta_field .= '<input type="' . $field_type . '" name="fitcase[' . $field_id_name . ']" id="' . $field_id_name . '" value="' . $radio . '" ' . checked( $meta[ $field_id_name ][0], $radio, false ) . ' />';
					$meta_field .= self::add_input_label( $field_id_name, $radio );
				}
			}

			// wp_editor() Instance
			if ( $field_type == 'wpeditor' ) {
				if ( isset( $meta[ $field_id_name ] ) ) {
					$editor_content = $meta[ $field_id_name ][0];
				} else {
					$editor_content = '';
				}

				$wpeditor_options['textarea_name'] = 'fitcase[' . $field_id_name . ']';

				wp_editor( $editor_content, $field_id_name, $wpeditor_options );
			}

			// Return Completed Meta Field
			return $meta_field;
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

		/**
		 * Converts a provided word into its plural form
		 *
		 * @since 1.0
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
