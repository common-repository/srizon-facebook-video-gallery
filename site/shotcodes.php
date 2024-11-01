<?php
add_shortcode( 'srizonfbvid', 'srizonfbvid_shortcode_callback' );
add_shortcode( 'srizonfbvidsingle', 'srizonfbvidsingle_shortcode_callback' );

function srizonfbvidsingle_shortcode_callback( $atts ) {
	if ( ! isset ( $atts['id'] ) ) {
		return 'Invalid Shortcode.';
	}
	$id = (int) $atts['id'];

	return srizon_view( dirname( __FILE__ ) . '/views/single.php', array( 'id' => $id ) );
}

function srizonfbvid_shortcode_callback( $atts ) {
	if ( ! srizonfbvid_shortcode_valid( $atts ) ) {
		return 'Incorrect Shortcode! Make sure a Gallery with this ID exists and is published.';
	}
	$p = srzfbvid_get_params( $atts['id'] );
	$p['id'] = (int) $atts['id'];

	$srizonFbVid = new SrizonFacebookVideo( $p['_srzfbvid_sync_after'] );

	$videos = $srizonFbVid->getVideos(
		$p['_srzfbvid_app_id'],
		$p['_srzfbvid_app_secret'],
		$p['_srzfbvid_long_token'],
		$atts['id'],
		$p['_srzfbvid_source_type'],
		$p['_srzfbvid_total_video'],
		$p['_srzfbvid_pageid']
	);

	if ( ! count( $videos ) ) {
		return __( "Couldn't Get any videos from Facebook. Check settings." );
	}

	return srizon_view( dirname( __FILE__ ) . '/views/' . $p['_srzfbvid_layout'] . '.php', array(
		'videos' => $videos,
		'p'      => $p
	) );
}

function srzfbvid_get_params( $id ) {
	$params = get_post_meta( $id ); // params from post meta
	if ( ! ( $params['_srzfbvid_source_type'][0] == 'profile' and $params['_srzfbvid_auth_type'][0] == 'new' ) ) {
		$params['_srzfbvid_long_token'][0] = cmb2_get_option( 'srizon_facebook_video_options', '_srzfbvid_long_token' );
	}
	if ( ! $params['_srzfbvid_long_token'][0] ) {
		$params['_srzfbvid_long_token'][0] = $params['_srzfbvid_app_id'][0] . '|' . $params['_srzfbvid_app_secret'][0];
	}
	foreach ( $params as $key => $value ) {
		$params[ $key ] = $params[ $key ][0];
	}

	return $params;
}

function srizonfbvid_shortcode_valid( $atts ) {
	if ( ! isset ( $atts['id'] ) ) {
		return false;
	}
	if ( 'srizon_fb_video' != get_post_type( $atts['id'] ) ) {
		return false;
	}
	if( 'publish' != get_post_status($atts['id'])){
		return false;
	}

	return true;
}
