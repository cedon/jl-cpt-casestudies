<?php

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
}
add_action( 'admin_menu', 'fitcase_init_options_page' );

function case_study_options_callback() {
	global $fitcase; ?>
	<div class="wrap">
		<h1><?php echo $fitcase->getPostType(); ?> Options</h1>

		<form method="post" action="options.php">

			<?php submit_button(); ?>
		</form>
	</div>
<?php }
