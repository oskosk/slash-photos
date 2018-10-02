<?php
/**
 * Plugin Name: Slash Photos
 * Description: Add a /photos URL to your site showing every image that you've ever published on your posts
 */
if ( ! defined( 'ABSPATH' ) ) {
        exit;
}

define( 'SLASH_PHOTOS_DIR', dirname( __FILE__ ) );
define( 'SLASH_PHOTOS_DEBUG', true );

class Photos {
	function __construct() {
		add_action( 'init', array( $this, 'init' ) );
	}

	function debug( $msg ) {
		if ( SLASH_PHOTOS_DEBUG ) {
			if ( gettype( $msg ) !== 'string' ) {
				$msg = print_r( $msg, true ) ;
			}
			error_log( $msg );
		}
	}

	function init() {
		$this->register_shortcode();
		$this->enqueue_scripts();
	}

	function enqueue_scripts() {
		wp_register_style(
		    'slash-photos',
		    plugins_url( 'slash-photos/styles.css', SLASH_PHOTOS_DIR ),
		    '1.0'
		);
	}

	function register_shortcode() {
		add_shortcode( 'photos', array( $this, 'shortcode' ) );
	}

	function shortcode( $attrs ) {
		wp_enqueue_style( 'slash-photos' );
		ob_start();
		$image_urls = $this->get_images();
		$this->debug( $image_urls );
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
		$this->debug($attached_images );
		$attached_images_urls = array_map( function( $img ) {
			return $img->guid;
		}, $attached_images );	
		error_log(print_r($attached_images, true ));
		return $attached_images_urls;
	}
}

new Photos();
