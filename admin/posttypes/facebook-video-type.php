<?php
// Register Custom Post Type
function srizon_fb_video_post_type() {

	$labels = array(
		'name'               => _x( 'Facebook Video Galleries', 'Post Type General Name', 'srizon_fb_vid' ),
		'singular_name'      => _x( 'Facebook Video Gallery', 'Post Type Singular Name', 'srizon_fb_vid' ),
		'menu_name'          => __( 'Facebook Video Galleries', 'srizon_fb_vid' ),
		'name_admin_bar'     => __( 'Facebook Video Gallery', 'srizon_fb_vid' ),
		'parent_item_colon'  => __( 'Parent Item:', 'srizon_fb_vid' ),
		'all_items'          => __( 'All Galleries', 'srizon_fb_vid' ),
		'add_new_item'       => __( 'Add New Facebook Video Gallery', 'srizon_fb_vid' ),
		'add_new'            => __( 'Add New', 'srizon_fb_vid' ),
		'new_item'           => __( 'New Gallery', 'srizon_fb_vid' ),
		'edit_item'          => __( 'Edit Facebook Video Gallery', 'srizon_fb_vid' ),
		'update_item'        => __( 'Update Gallery', 'srizon_fb_vid' ),
		'view_item'          => __( 'View Gallery', 'srizon_fb_vid' ),
		'search_items'       => __( 'Search Gallery', 'srizon_fb_vid' ),
		'not_found'          => __( 'Not found', 'srizon_fb_vid' ),
		'not_found_in_trash' => __( 'Not found in Trash', 'srizon_fb_vid' ),
	);

	$args = array(
		'label'               => __( 'Facebook Video', 'srizon_fb_vid' ),
		'description'         => __( 'Srizon Facebook Videos', 'srizon_fb_vid' ),
		'labels'              => $labels,
		'supports'            => array( 'title' ),
		'taxonomies'          => array(),
		'hierarchical'        => false,
		'public'              => false,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'menu_position'       => 100,
		'show_in_admin_bar'   => true,
		'show_in_nav_menus'   => true,
		'can_export'          => true,
		'has_archive'         => false,
		'exclude_from_search' => false,
		'publicly_queryable'  => true,
		'capability_type'     => 'post',
		'menu_icon'           => 'dashicons-video-alt',
	);
	register_post_type( 'srizon_fb_video', $args );

}


function srizon_fb_video_columns_header( $defaults ) {
	$defaults['shortcode'] = 'ShortCode';
	$defaults['id'] = 'Gallery ID';
	unset( $defaults['date'] );

	return $defaults;
}

function srizon_fb_video_columns_content( $column_name, $post_id ) {
	if ( $column_name == 'shortcode' ) {
		echo <<<END
<input type="text" value="[srizonfbvid id={$post_id}]" name="shortcode-{$post_id}" />
END;

	}
	if($column_name == 'id'){
		echo $post_id;
	}
}

function srizon_fb_video_script() {
	global $post_type;
	if ( 'srizon_fb_video' == $post_type ) {
		wp_enqueue_script( 'srzfbvidadmin', WP_PLUGIN_URL . '/srizon-facebook-video-gallery/resources/js/admin.js', array( 'jquery' ), '1.0' );
	}
	wp_enqueue_style( 'srzfbvidadmin', WP_PLUGIN_URL . '/srizon-facebook-video-gallery/resources/css/admin.css', null, '1.0' );
}

add_action( 'admin_print_scripts-post-new.php', 'srizon_fb_video_script', 11 );
add_action( 'admin_print_scripts-post.php', 'srizon_fb_video_script', 11 );
add_action( 'init', 'srizon_fb_video_post_type', 0 );
add_filter( 'manage_srizon_fb_video_posts_columns', 'srizon_fb_video_columns_header' );
add_filter( 'manage_srizon_fb_video_posts_custom_column', 'srizon_fb_video_columns_content', 10, 2 );