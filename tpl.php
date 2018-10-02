<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="slash-photos-container">
	<div class="grid">
		<?php
			foreach( $image_urls as $image ) {
			?>
				<div class="slash-photos-image cell">
					<a href="#">
						<img src="<?php echo $image; ?>" />
					</a>
				</div>
			<?php
			}
		?>
	</div>
</div>
