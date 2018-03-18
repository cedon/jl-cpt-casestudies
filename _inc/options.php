<?php

// Register options for the custom post type
function fitcase_register_options() {
	global $fitcase;
	$post_type_name = str_replace('_', '-', $fitcase->getPostTypeName() );
	$options_group = $post_type_name . '-options-group';

	register_setting( $options_group, JLFITCASE__NAMESPACE . '_test_option' );

}

// Create Options page as submenu page of the custom post type
function fitcase_init_options_page() {
	global $fitcase;
	add_submenu_page(
		'edit.php?post_type=' . $fitcase->getPostTypeName(),
		$fitcase->getPostType() . ' Options',
		'Options',
		'manage_options',
		$fitcase->getPostTypeName() . '_options',
		$fitcase->getPostTypeName() . '_options_callback'
	);

	// Call Register Settings Function
	add_action( 'admin_init', 'fitcase_register_options' );
}
add_action( 'admin_menu', 'fitcase_init_options_page' );

function case_study_options_callback() {
	global $fitcase;
	$post_type_name = str_replace('_', '-', $fitcase->getPostTypeName() );
	$options_group = $post_type_name . '-options-group';
	?>

	<div class="wrap">
		<h1><?php echo $fitcase->getPostType(); ?> Options</h1>

		<form method="post" action="options.php">
			<?php settings_fields( $options_group ); ?>
			<?php do_settings_sections( $options_group ); ?>

			<table class="form-table">
				<tr align="top">
					<th scope="row">Test Option</th>
					<td>
						<input type="text" name="<?php echo JLFITCASE__NAMESPACE . '_test_option'; ?>" value="<?php
						echo esc_attr( get_option( JLFITCASE__NAMESPACE . '_test_option' ) ); ?>"/>
					</td>
				</tr>
			</table>

			<?php submit_button(); ?>

		</form>

		<div>
			<?php get_fitcase_options(); ?>
		</div>
	</div>
<?php }
