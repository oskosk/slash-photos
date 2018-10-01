<?php
/**
 * Plugin Name: Slash Photos
 * Description: Show all the photos from your posts in just one page.
 */

define( 'SLASH_PHOTOS_DIR', dirname( __FILE__ ) );

class Photos {
	function __construct() {
		add_action( 'init', array( $this, 'init' ) );
	}

	function init() {
		error_log('titan');
		$this->register_shortcode();
		$this->enqueue_scripts();
	}

	function enqueue_scripts() {
		wp_register_style(
		    'slash-photos',
		    plugins_url( 'slash-photos/styles.css', SLASH_PHOTOS_DIR ),
		    '1.0', // version number
		    'screen' // CSS media type
		);
		wp_enqueue_style( 'slash-photos' );
	}

	function register_shortcode() {
		add_shortcode( 'photos', array( $this, 'shortcode' ) );
	}

	function shortcode( $attrs ) {
		ob_start();
		$images = $this->get_images();
		include( 'tpl.php' );
		return ob_get_clean();   
	}

	function get_images() {
		$posts = $this->get_all_posts();
		$images = [];
		foreach( $posts as $post ) {
			$img = $this->get_images_for_post( $post->ID );	
			$images = array_merge( $images, $img );
		}
		return $images;
	}

	function get_all_posts() {
		return get_posts();
	}
	
	function get_images_for_post( $pid = 146 ) {
		$args = array( 
		    'post_type' => 'attachment', 
		    'post_mime_type' => 'image',
		    'numberposts' => -1, 
		    'post_status' => null, 
		    'post_parent' => $pid 
		); 
		$attached_images = get_posts( $args );
		$attached_images_urls = array_map( function( $img ) {
			return $img->guid;
		}, $attached_images );	
		error_log(print_r($attached_images, true ));
		return $attached_images_urls;
	}
}

new Photos();
