<?php
add_action( 'cmb2_admin_init', 'srizon_register_srizon_vid_metabox' );
/**
 * Hook in and add a demo metabox. Can only happen on the 'cmb2_admin_init' or 'cmb2_init' hook.
 */
function srizon_register_srizon_vid_metabox() {
//	+Source Type Metabox
	$prefix = SrizonFbVidDefaults::$meta_prefix;

	$srizon_fbvid_source = new_cmb2_box(
		array(
			'id'           => $prefix . 'source_metabox',
			'title'        => __( 'Video Source', 'srizon_fb_vid' ),
			'object_types' => array( 'srizon_fb_video', ),
		)
	);

	$srizon_fbvid_source->add_field(
		array(
			'name'       => __( 'Page or Profile' ),
			'id'         => $prefix . 'source_type',
			'type'       => 'radio',
			'default'    => 'page',
			'options'    => array(
				'page'    => 'A <strong>Page</strong> (Also Called Fan/Business Page) or a <strong>Playlist</strong> from a Page',
				'profile' => 'My Profile (Where you add friends)',
			),
			'before_row' => 'srz_fbvid_check_source',
		)
	);

	$srizon_fbvid_source->add_field(
		array(
			'name'        => __( 'Page ID / Playlist ID' ),
			'id'          => $prefix . 'pageid',
			'desc'        => __( 'ID of your Page/Playlist ( <a href="http://www.srizon.com/srizon-facebook-album-documentation#getting-page-id" target="_blank">How to get Page ID</a> - <a href="https://www.youtube.com/watch?v=UD8DjaFd8wg" target="_blank">How to get Playlist ID</a>)' ),
			'type'        => 'text',
			'row_classes' => 'srz-cond',
			'attributes'  => array(
				'data-cond-option' => $prefix . 'source_type',
				'data-cond-value'  => 'page',
			),
		)
	);

	$srizon_fbvid_source->add_field(
		array(
			'name'        => __( 'User Token Source' ),
			'id'          => $prefix . 'auth_type',
			'type'        => 'radio',
			'default'     => 'settings',
			'options'     => array(
				'settings' => 'Use The Authentication from <a href="options-general.php?page=srizon_facebook_video_options">Settings page</a>',
				'new'      => 'New Authentication',
			),
			'row_classes' => 'srz-cond',
			'attributes'  => array(
				'data-cond-option' => $prefix . 'source_type',
				'data-cond-value'  => 'profile',
			),
		)
	);

	$srizon_fbvid_source->add_field(
		array(
			'name'        => __( 'User Access Token', 'srizon_facebook_video' ),
			'desc'        => __( 'You may need to enter your App ID and Secret you created for this domain on the <a href="options-general.php?page=srizon_facebook_video_options">Settings page</a>', 'srizon_facebook_video' ),
			'id'          => $prefix . 'app_token',
			'type'        => 'text',
			'before'      => ' <a href="javascript:;" class="button-primary" id="srzFBloginbutton">' . __( "Loading FB Scripts..." ) . '</a>',
			'row_classes' => 'srz-cond',
			'attributes'  => array(
				'data-cond-option' => $prefix . 'auth_type',
				'data-cond-value'  => 'new',
			),
		)
	);

	$app_id     = srizon_facebook_video_get_option( $prefix . 'app_id' );
	$app_secret = srizon_facebook_video_get_option( $prefix . 'app_secret' );

	$srizon_fbvid_source->add_field(
		array(
			'id'         => $prefix . 'app_id',
			'type'       => 'hidden',
			'attributes' => array(
				'value' => $app_id,
			)
		)
	);
	$srizon_fbvid_source->add_field(
		array(
			'id'         => $prefix . 'app_secret',
			'type'       => 'hidden',
			'attributes' => array(
				'value' => $app_secret,
			)
		)
	);
	$srizon_fbvid_source->add_field(
		array(
			'id'   => $prefix . 'long_token',
			'type' => 'hidden',
		)
	);
//	-Source Type Metabox

//	+Shortcode Type Metabox
	$srizon_fbvid_shortcode = new_cmb2_box(
		array(
			'id'           => $prefix . 'shortcode_metabox',
			'title'        => __( 'Shortcode', 'srizon_fb_vid' ),
			'object_types' => array( 'srizon_fb_video', ),
			'context'      => 'side',
			'priority'     => 'low',
		)
	);
	$srizon_fbvid_shortcode->add_field(
		array(
			'name'      => __( 'Shortcode for this Gallery' ),
			'id'        => $prefix . 'code',
			'type'      => 'title',
			'after_row' => 'srz_fb_vid_get_post_shortcode',

		)
	);


//	-Shortcode Type Metabox

//	+Options type Metabox
	$srizon_fbvid_options = new_cmb2_box(
		array(
			'id'           => $prefix . 'options_metabox',
			'title'        => __( 'Options', 'srizon_fb_vid' ),
			'object_types' => array( 'srizon_fb_video' ),

		)
	);

	$srizon_fbvid_options->add_field(
		array(
			'name'    => __( 'Number of Videos', 'srizon_fb_vid' ),
			'id'      => $prefix . 'total_video',
			'type'    => 'text',
			'default' => '10',
		)
	);
	$srizon_fbvid_options->add_field(
		array(
			'name'    => __( 'Sync after every # minutes', 'srizon_fb_vid' ),
			'id'      => $prefix . 'sync_after',
			'type'    => 'text',
			'default' => '60',
		)
	);

//	-Options type Metabox

//	+Layout type Metabox

	$srizon_fbvid_layout = new_cmb2_box(
		array(
			'id'           => $prefix . 'layout_metabox',
			'title'        => __( 'Layout Related', 'srizon_fb_vid' ),
			'object_types' => array( 'srizon_fb_video' ),
		)
	);

	$srizon_fbvid_layout->add_field(
		array(
			'name'    => __( 'Select Layout' ),
			'id'      => $prefix . 'layout',
			'type'    => 'radio',
			'default' => 'videolistdesc',
			'options' => array(
				'videolist'     => 'Video List',
				'videolistdesc'     => 'Video List with Description',
			),
		)
	);

	$srizon_fbvid_layout->add_field(
		array(
			'name'    => __( 'Paginate After every # items', 'srizon_fb_vid' ),
			'id'      => $prefix . 'paginate_after',
			'type'    => 'text',
			'default' => '2',
		)
	);
//	-Layout type Metabox

}

function srz_fb_vid_get_post_shortcode() {
	if ( is_admin() ) {
		global $post;
		if ( $post->post_status == 'publish' ) {
			$code = <<<END
<p>Copy the shortcode below and use it on a page/post</p>
<input type="text" value="[srizonfbvid id={$post->ID}]" />
END;

			return $code;
		}
	}

	return 'Publish this to get the shortcode';
}

/**
 * Shows a message before the first field by checking the api call with provided source/auth setup
 * @return string success or error message
 */
function srz_fbvid_check_source() {
	global $post;
	$prefix = SrizonFbVidDefaults::$meta_prefix;
	if ( $post->post_status == 'publish' ) {
		$sourcetype = get_post_meta( $post->ID, $prefix . 'source_type', true );
		if ( $sourcetype == 'page' ) {
			$pageid = get_post_meta( $post->ID, $prefix . 'pageid', true );
			if ( ! trim( $pageid ) ) {
				return '<h4 class="dashicons-before dashicons-no dashicon-error">Page ID is not set!</h4>';
			}

			return srz_fbvid_try( '/' . $pageid . '/videos?fields=id&limit=3' );
		} else if ( $sourcetype == 'profile' ) {
			return srz_fbvid_try( '/videos/uploaded?fields=id&limit=3', true );
		}
	}

	return '';
}

/**
 * @param $endpoint string Facebook endpoint to be checked
 * @param bool|false $user pass true if profile/user needs to be checked
 *
 * @return string success or error message to be displayed
 */
function srz_fbvid_try( $endpoint, $user = false ) {
	global $post;
	$prefix           = SrizonFbVidDefaults::$meta_prefix;
	$app_id_field     = $prefix . 'app_id';
	$app_secret_field = $prefix . 'app_secret';
	$long_token_field = $prefix . 'long_token';

	$auth_type       = get_post_meta( $post->ID, $prefix . 'auth_type', true );
	$source_type     = get_post_meta( $post->ID, $prefix . 'source_type', true );
	$post_long_token = get_post_meta( $post->ID, $prefix . 'long_token', true );

	$app_id     = srizon_facebook_video_get_option( $app_id_field );
	$app_secret = srizon_facebook_video_get_option( $app_secret_field );
	if(!$app_id) $app_id = SrizonFbVidDefaults::$app_id;
	if(!$app_secret) $app_secret = SrizonFbVidDefaults::$app_secret;
	$long_token = srizon_facebook_video_get_option( $long_token_field );

	if ( $auth_type == 'new' and $source_type == 'profile' ) {
		$long_token = $post_long_token;
	}
	$fb = new \Facebook\Facebook( [
		'app_id'                => $app_id,
		'app_secret'            => $app_secret,
		'default_graph_version' => 'v2.5'
	] );
	if ( $long_token ) {
		$fb->setDefaultAccessToken( $long_token );
	} else {
		$fb->setDefaultAccessToken( $app_id . '|' . $app_secret );
	}
	try {
		if ( $user ) {
			$res      = $fb->get( '/me' );
			$userid   = $res->getDecodedBody()['id'];
			$endpoint = '/' . $userid . $endpoint;
		}
		$res       = $fb->get( $endpoint );
		$total_vid = count( $res->getDecodedBody()['data'] );

		return "<h4 class='dashicons-before dashicons-yes dashicon-success'>App and Video source setup seems ok. API call is getting data from Facebook. {$total_vid} videos has been returned on a test API call for 3 videos</h4>";
	} catch ( Exception $e ) {

		return "<h4 class='dashicons-before dashicons-no dashicon-error'>Video Source and/or <a href=\"options-general.php?page=srizon_facebook_video_options\">App Setup</a> might be wrong!<br> Facebook's Response: {$e->getMessage()}</h4>";
	}
}

add_action( 'cmb2_post_process_fields__srzfbvid_source_metabox', 'srzfbvid_before_save_srizon_fb_video' );

function srzfbvid_before_save_srizon_fb_video( $vals ) {
	global $post;
	$prefix = SrizonFbVidDefaults::$meta_prefix;
	$srzfb  = new SrizonFacebookVideo( $vals->data_to_save [ $prefix . 'sync_after' ] );
	$srzfb->cacheClean(
		$post->ID,
		$vals->data_to_save [ $prefix . 'source_type' ],
		$vals->data_to_save [ $prefix . 'pageid' ]
	);
	if ( ! trim( $vals->data_to_save[ $prefix . 'app_id' ] ) ) {
		$vals->data_to_save[ $prefix . 'app_id' ] = SrizonFbVidDefaults::$app_id;
	}
	if ( ! trim( $vals->data_to_save[ $prefix . 'app_secret' ] ) ) {
		$vals->data_to_save[ $prefix . 'app_secret' ] = SrizonFbVidDefaults::$app_secret;
	}
	$fb = new \Facebook\Facebook( [
		'app_id'                => $vals->data_to_save[ $prefix . 'app_id' ],
		'app_secret'            => $vals->data_to_save[ $prefix . 'app_secret' ],
		'default_graph_version' => 'v2.5'
	] );
	if ( trim( $vals->data_to_save[ $prefix . 'app_token' ] ) ) {
		try {
			$vals->data_to_save[ $prefix . 'long_token' ] = $fb->getOAuth2Client()->getLongLivedAccessToken( $vals->data_to_save[ $prefix . 'app_token' ] );

		} catch ( Exception $e ) {
			$vals->data_to_save[ $prefix . 'long_token' ] = '';
		}
	} else {
		$vals->data_to_save[ $prefix . 'long_token' ] = '';
	}

	return $vals;
}