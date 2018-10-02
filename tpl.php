<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="slash-photos-container">
		<?php
			foreach( $image_urls as $image ) {
			?>
				<div class="thumbnail">
					<a target="_blank" rel="noopener noreferrer" href="<?php echo get_permalink( $image['post_parent'] );?>">
						<img src="<?php echo $image['thumbnail_url']; ?>" />
					</a>
				</div>
			<?php
			}
		?>
</div>
