<?php
add_filter( 'the_content', 'srizon_process_demo_type' );
function srizon_process_demo_type( $content ) {
	if ( get_post_type( get_the_ID() ) != 'srizon_fb_video' ) {
		return $content;
	}
	$msg = <<<END
<p>This is a custom post for Srizon Facebook Video Gallery</p>
<p>This post isn't supposed to be viewed directly. Copy the shortcode for this post from admin area and use it on another post/page</p>
END;


	return $content . $msg;
}