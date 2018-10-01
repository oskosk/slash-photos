<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="slash-photos-container masonry">
<?php
	foreach( $images as $image ) {
	?>
		<div class="slash-photos-image">
			<a href="#">
				<img src="<?php echo $image; ?>" />
			</a>
		</div>
	<?php
	}
?>
	Alo
</div>
