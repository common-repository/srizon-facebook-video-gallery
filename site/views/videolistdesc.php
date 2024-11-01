<div class="srzfbvid-block-wrap">
	<?php
	$i = 0;
	foreach ( $videos as $video ) {
		if ( $i < $p['_srzfbvid_paginate_after'] ) {
			$class = 'fb-video';
			$class_desc = 'fb-video-desc';
		} else {
			$class = 'fb-video-hidden';
			$class_desc = 'fb-video-desc-hidden';

		}
		$i ++;
		?>
		<div class="srzfb-video-wrap">

			<div class="<?php echo $class_desc; ?>">
				<?php
				if ( isset( $video['title'] ) and strlen( trim( $video['title'] ) ) ) {
					echo '<h3>' . $video['title'] . '</h3>';
				}
				if ( isset( $video['description'] ) and strlen( trim( $video['description'] ) ) ) {
					echo '<p>' . links_add_target( make_clickable( $video['description'] ) ) . '</p>';
				}
				?>
			</div>

			<div class="<?php echo $class; ?>"
			     data-href="https://www.facebook.com/video.php?v=<?php echo $video['id']; ?>"
			     data-allowfullscreen="true"></div>
		</div>
		<?php
	}
	?>
</div>
<?php
if ( $i > $p['_srzfbvid_paginate_after'] ) {
	?>
	<div class="srzfbvid-load-more">
		<button class="srzfbvid-load-more"
		        data-load="<?php echo $p['_srzfbvid_paginate_after']; ?>"><?php echo __( 'Load More' ) ?></button>
	</div>
	<?php
}