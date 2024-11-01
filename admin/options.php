<?php

/**
 * CMB2 Theme Options
 * @version 0.1.0
 */
class SrizonFacebookVideoAdmin {

	/**
	 * Option key, and option page slug
	 * @var string
	 */
	private $key = 'srizon_facebook_video_options';

	/**
	 * Options page metabox id
	 * @var string
	 */
	private $metabox_id = 'srizon_facebook_video_options';

	/**
	 * Options Page title
	 * @var string
	 */
	protected $title = '';

	/**
	 * Options Page hook
	 * @var string
	 */
	protected $options_page = '';

	/**
	 * Constructor
	 * @since 0.1.0
	 */
	public function __construct() {
		// Set our title
		$this->title = __( 'Srizon Facebook Video Options', 'srizon_facebook_video' );
	}

	/**
	 * Initiate our hooks
	 * @since 0.1.0
	 */
	public function hooks() {
		add_action( 'admin_init', array( $this, 'init' ) );
		add_action( 'admin_menu', array( $this, 'add_options_page' ) );
		add_action( 'cmb2_init', array( $this, 'add_options_page_metabox' ) );
	}


	/**
	 * Register our setting to WP
	 * @since  0.1.0
	 */
	public function init() {
		register_setting( $this->key, $this->key );
	}

	/**
	 * Add menu options page
	 * @since 0.1.0
	 */
	public function add_options_page() {
		// Add sub-menu under settings for common options
		$this->options_page = add_submenu_page( 'options-general.php', $this->title, $this->title, 'manage_options', $this->key, array(
			$this,
			'admin_page_display'
		) );

		// Include CMB CSS in the head to avoid FOUT
		add_action( "admin_print_styles-{$this->options_page}", array( 'CMB2_hookup', 'enqueue_cmb_css' ) );
		add_action( "admin_print_scripts-{$this->options_page}", array( $this, 'load_js_files' ) );
	}

	public function load_js_files() {
		wp_enqueue_script( 'srzfbvidadmin', WP_PLUGIN_URL . '/srizon-facebook-video-gallery/resources/js/admin.js', array( 'jquery' ), '1.0' );
	}


	/**
	 * Admin page markup. Mostly handled by CMB2
	 * @since  0.1.0
	 */
	public function admin_page_display() {
		?>
		<div class="wrap cmb2-options-page <?php echo $this->key; ?>">
			<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
			<?php cmb2_metabox_form( $this->metabox_id, $this->key ); ?>
		</div>
		<?php
	}

	/**
	 * Add the options metabox to the array of metaboxes
	 * @since  0.1.0
	 */
	function add_options_page_metabox() {
		$prefix = SrizonFbVidDefaults::$meta_prefix;

		$cmb = new_cmb2_box( array(
			'id'         => $this->metabox_id,
			'hookup'     => false,
			'cmb_styles' => false,
			'show_on'    => array(
				// These are important, don't remove
				'key'   => 'options-page',
				'value' => array( $this->key, )
			),
		) );

		// Set our CMB2 fields

		$cmb->add_field( array(
			'name'       => __( 'Facebook App ID', 'srizon_facebook_video' ),
			'desc'       => __( 'Provide your facebook App ID (get yours from <a target="_blank" href="http://developers.facebook.com">Developers.Facebook</a>)', 'srizon_facebook_video' ),
			'id'         => $prefix . 'app_id',
			'type'       => 'text',
			'before_row' => 'srzfbvid_check_token_validity',
			'default'    => SrizonFbVidDefaults::$app_id,
			'attributes' => array(
				'required' => 'required'
			),
		) );

		$cmb->add_field( array(
			'name'       => __( 'Facebook App Secret', 'srizon_facebook_video' ),
			'desc'       => __( 'Provide your facebook App Secret', 'srizon_facebook_video' ),
			'id'         => $prefix . 'app_secret',
			'type'       => 'text',
			'default'    => SrizonFbVidDefaults::$app_secret,
			'attributes' => array(
				'required' => 'required'
			),
		) );

		$cmb->add_field(
			array(
				'name'   => __( 'User Access Token', 'srizon_facebook_video' ),
				'desc'   => __( 'In order to get user access token your fb app should be set-up properly with the domain of this website. <br> Default App ID and Secret won\'t work as they are not set-up for your domain. <br>Create your own app for your domain. <a target="_blank" href="https://www.youtube.com/watch?v=QMGSgxlux4c">How-to video</a>', 'srizon_facebook_video' ),
				'id'     => $prefix . 'app_token',
				'type'   => 'text',
				'before' => ' <a href="javascript:;" class="button-primary" id="srzFBloginbutton">' . __( "Loading FB Scripts..." ) . '</a>',
			)
		);


		$cmb->add_field(
			array(
				'id'   => $prefix . 'long_token',
				'type' => 'hidden',
			)
		);

//		Todo: maybe in the future
//		$cmb->add_field(
//			array(
//				'name' => __( 'Email for expiry notification', 'srizon_facebook_video' ),
//				'desc' => __( 'A message will be sent to this email address when the token needs to be re-newed (normally around 60 days)', 'srizon_facebook_video' ),
//				'id'   => $prefix . 'fb_token_expiry_email',
//				'type' => 'text_email',
//			)
//		);

	}

	/**
	 * Public getter method for retrieving protected/private variables
	 * @since  0.1.0
	 *
	 * @param  string $field Field to retrieve
	 *
	 * @return mixed          Field value or exception is thrown
	 */
	public function __get( $field ) {
		// Allowed fields to retrieve
		if ( in_array( $field, array( 'key', 'metabox_id', 'title', 'options_page' ), true ) ) {
			return $this->{$field};
		}

		throw new Exception( 'Invalid property: ' . $field );
	}

}

/**
 * Helper function to get/return the Srizon_Admin object
 * @since  0.1.0
 * @return Srizon_Admin object
 */
function srizon_facebook_video_admin() {
	static $object = null;
	if ( is_null( $object ) ) {
		$object = new SrizonFacebookVideoAdmin();
		$object->hooks();
	}

	return $object;
}

/**
 * Wrapper function around cmb2_get_option
 * @since  0.1.0
 *
 * @param  string $key Options array key
 *
 * @return mixed        Option value
 */
function srizon_facebook_video_get_option( $key = '' ) {
	return cmb2_get_option( srizon_facebook_video_admin()->key, $key );
}

// Get it started
srizon_facebook_video_admin();

add_action( 'cmb2_options-page_process_fields_srizon_facebook_video_options', 'srzfbvid_before_save_options' );

/**
 * @param $vals  array array of values to be saved
 * It also prints some admin notices
 *
 * @return mixed modified values
 */
function srzfbvid_before_save_options( $vals ) {
	$prefix           = SrizonFbVidDefaults::$meta_prefix;
	$app_id_field     = $prefix . 'app_id';
	$app_secret_field = $prefix . 'app_secret';
	$app_token_field  = $prefix . 'app_token';
	$long_token_field = $prefix . 'long_token';
	$fb               = new \Facebook\Facebook( [
		'app_id'                => $vals->data_to_save[ $app_id_field ],
		'app_secret'            => $vals->data_to_save[ $app_secret_field ],
		'default_graph_version' => 'v2.5'
	] );
	if ( trim( $vals->data_to_save[ $app_token_field ] ) ) {
		try {
			$vals->data_to_save[ $long_token_field ] = $fb->getOAuth2Client()->getLongLivedAccessToken( $vals->data_to_save[ $app_token_field ] );

		} catch ( Exception $e ) {
			$vals->data_to_save[ $long_token_field ] = '';
			echo '<div class="error"><h4>' . $e->getMessage() . '</h4></div>';
		}
	} else {
		$vals->data_to_save[ $long_token_field ] = '';
	}

	echo "<div class=\"updated notice notice-success is-dismissible\"><h4>" . __( "Settings saved." ) . "</h4></div>";

	return $vals;
}

/**
 * @return callback function for the first field before. It checks the provided app id / secret / token and gives a message accordingly
 */
function srzfbvid_check_token_validity() {
	$prefix           = SrizonFbVidDefaults::$meta_prefix;
	$app_id_field     = $prefix . 'app_id';
	$app_secret_field = $prefix . 'app_secret';
	$long_token_field = $prefix . 'long_token';

	$app_id     = srizon_facebook_video_get_option( $app_id_field );
	$app_secret = srizon_facebook_video_get_option( $app_secret_field );
	$long_token = srizon_facebook_video_get_option( $long_token_field );

	if ( $long_token ) {
		try {
			$fb = new \Facebook\Facebook( [
				'app_id'                => $app_id,
				'app_secret'            => $app_secret,
				'default_graph_version' => 'v2.5'
			] );

			$fb->setDefaultAccessToken( $long_token );
			$me = $fb->get( '/me' );
		} catch ( Exception $e ) {
			return "<div class=\"error\"><h2>{$e->getMessage()}</h2></div>";

		}

		return "<div class=\"updated notice notice-success\"><h4>" . __( "Token Seems Valid. Renew it every month for regular sync." ) . "</h4></div>";
	} else if ( trim( $app_id ) and trim( $app_secret ) ) {
		try {
			$fb = new \Facebook\Facebook( [
				'app_id'                => $app_id,
				'app_secret'            => $app_secret,
				'default_graph_version' => 'v2.5'
			] );
			$fb->setDefaultAccessToken( $app_id . '|' . $app_secret );
			$nadal = $fb->get( '/nadal' );
		} catch ( Exception $e ) {
			return "<div class=\"error\"><h2>" . __( "Invalid AppID and Secret. Reply from FB" ) . " : <small>{$e->getMessage()}</small></h2></div>";
		}

		return '<div class="notice notice-warning"><h4>' . __( 'App ID and Secret seems Valid but User Access Token was not provided/expired. The app will only be able to work with public/unrestricted &quot;FB Pages&quot;. Get a token and give the app more power.' ) . '</h4></div>';

	}

}
