<?php
add_action( 'admin_head', 'srzfb_add_single_video_button' );

function srzfb_add_single_video_button() {
	global $typenow;
	// check user permissions
	if ( ! current_user_can( 'edit_posts' ) && ! current_user_can( 'edit_pages' ) ) {
		return;
	}
	// verify the post type
	if ( ! in_array( $typenow, array( 'post', 'page' ) ) ) {
		return;
	}
	// check if WYSIWYG is enabled
	if ( get_user_option( 'rich_editing' ) == 'true' ) {
		add_filter( "mce_external_plugins", "srzfb_single_video_add_tinymce_plugin" );
		add_filter( 'mce_buttons', 'srzfb_single_video_register_button' );
	}
}

function srzfb_single_video_add_tinymce_plugin($plugin_array) {
	$plugin_array['srzfb_single_video_button'] = WP_PLUGIN_URL . '/srizon-facebook-video-gallery/resources/js/button.js';

	return $plugin_array;
}

function srzfb_single_video_register_button( $buttons ) {
	array_push( $buttons, "srzfb_single_video_button" );

	return $buttons;
}