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
			$this->post_type_name      = strtolower( str_replace( ' ','_', $name) );
			$this->post_type_args      = $args;
			$this->post_type_lables    = $labels;

			// Add Action to Register Custom Post Type if it Does Not Already Exist
			if( ! post_type_exists( $this->post_type_name) ) {
				add_action( 'init', array( &$this, 'register_post_type' ) );
			}

			// Listen for Save Post Hook
			$this->save();
		}

		/** Register post type method */
		public function register_post_type() {

			// Capitalize words and make them plural
			$name   = ucwords( str_replace( '_', ' ', $this->post_type_name ) );
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
				),

				// Overrides
				$this->post_type_args
			);

			// Create the Custom Post Type
			register_post_type( $this->post_type_name, $args );

		}

		/** Register taxonomy method */
		public function add_taxonomy( $name, $args = array(), $labels = array() ) {

			if( ! empty( $name ) ) {

				// Get Post Name
				$post_type_name = $this->post_type_name;

				// Taxonomy Properties
				$taxonomy_name   = strtolower( str_replace( ' ', '_', $name ) );
				$taxonomy_labels = $labels;
				$taxonomy_args   = $args;

				if( ! taxonomy_exists( $taxonomy_name ) ) {

					// Capitalize words and make them plural
					$name   = ucwords( str_replace( '_', ' ', $name ) );
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

		/** Attaches meta boxes to the post type */
		public function add_meta_box( $title, $fields = array(), $context = 'normal', $priority = 'default' ) {

		}

		/** Listener for saving post */
		public function save() {


		}

		/** Beautify Helper Function */
		public static function beautify( $string ) {
			return ucwords( str_replace( '_', ' ', $string ) );
		}

		/** Uglify Helper Function */
		public static function uglify( $string ) {
			return strtolower( str_replace( ' ', '_', $string ) );
		}

		/** Pluralize Helper Function */
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
