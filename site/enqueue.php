<?php
// frontend related hooks goes here

//add_action( 'wp_enqueue_scripts', 'your_enqueue_script_function' );
add_action( 'wp_enqueue_scripts', 'srzfbvid_site_scripts' );

function srzfbvid_site_scripts() {
	wp_enqueue_script(
		'srzfb-site',
		WP_PLUGIN_URL . '/srizon-facebook-video-gallery/resources/js/site.js',
		array( 'jquery' ),
		'1.0'
	);
	wp_enqueue_style(
		'srzfb-site',
		WP_PLUGIN_URL . '/srizon-facebook-video-gallery/resources/css/site.css',
		null,
		'1.0'
	);
}

