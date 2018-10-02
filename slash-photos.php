<?php
/**
 * Plugin Name: Slash Photos
 * Description: Add a /photos URL to your site showing every image that you've ever published on your posts
 */
if ( ! defined( 'ABSPATH' ) ) {
        exit;
}

define( 'SLASH_PHOTOS_DIR', dirname( __FILE__ ) );
// Set to true for logging some stuff via error_log
define( 'SLASH_PHOTOS_DEBUG', true );


class Photos {
	function __construct() {
		add_action( 'init', array( $this, 'init' ) );
	}

	function error_log( $msg ) {
		// Do nothing if WP is not set for debugging
		if ( ! ( defined( 'WP_DEBUG' ) && WP_DEBUG ) ) {
			return;
		}
		if ( SLASH_PHOTOS_DEBUG ) {
			if ( gettype( $msg ) !== 'string' ) {
				$msg = print_r( $msg, true ) ;
			}
			error_log( $msg );
		}
	}
	/*
	 * Hook all the things
	 */
	function init() {
		$this->register_shortcode();
		$this->enqueue_scripts();
		$this->maybe_create_photos_page();
	}

	/**
	 * Register stylesheet for enqueuing later if the shortcode is used.
	 */
	function enqueue_scripts() {
		wp_register_style(
		    'slash-photos',
		    plugins_url( 'slash-photos/styles.css', SLASH_PHOTOS_DIR ),
		    [ 'media-views' ],
		    '1.0'
		);
	}

	/**
	 * Register shortcode [photos]
	 */
	function register_shortcode() {
		add_shortcode( 'photos', array( $this, 'shortcode' ) );
	}

	/**
	 * callback for add_shortcode
	 */
	function shortcode( $attrs ) {
		wp_enqueue_style( 'slash-photos' );
		ob_start();
		$image_urls = $this->get_images();
		include( 'tpl.php' );
		return ob_get_clean();
	}

	/**
	 * Get all images attached to posts
	 */
	function get_images() {
		$posts = $this->get_all_posts();
		$images = [];
		foreach( $posts as $post ) {
			$img = $this->get_images_for_post( $post->ID );
			$images = array_merge( $images, $img );
		}
		return $images;
	}

	/**
	 * Get all published posts
	 */
	function get_all_posts() {
		return get_posts();
	}

	/**
	 * Get all images attached to a post
	 */
	function get_images_for_post( $pid ) {
		$args = array(
		    'post_type' => 'attachment',
		    'post_mime_type' => 'image',
		    'numberposts' => -1,
		    'post_status' => null,
		    'post_parent' => $pid
		);
		$attached_images = get_posts( $args );
		$attached_images_urls = array_map( function( $img ) {
			return [
				'url' => $img->guid,
				'thumbnail_url' => wp_get_attachment_thumb_url( $img->ID ),
				'post_parent' => $img->post_parent,
			];
		}, $attached_images );

		return $attached_images_urls;
	}

	function maybe_create_photos_page() {
		if ( ! $this->photos_page_exists() ) {
			$this->create_photos_page();
		} else {
			$this->error_log( 'it does exist' );
		}
	}

	/**
	 * Checks if a page with the /photos slug already exists
	 */
	function photos_page_exists() {
		$slug = $this->photos_page_slug();
		return false != get_page_by_path( $slug );
	}
	/**
	 * Creates a page with the /photos slug
	 */
	function create_photos_page() {

	}
	/**
	 * Returns the photos page slug. Defaults to /photos
	 */
	function photos_page_slug() {
		$slug = apply_filters( 'slash-photos_page-slug', '/photos' );
		return $slug;
	}

	/**
	 * Returns the photos page default title. Defaults to __( 'Photos' )
	 */
	function photos_page_default_title() {
		$slug = apply_filters( 'slash-photos_page-title', __( 'Photos', 'slash-photos' ) );
		return $slug;
	}
}

// Do the thing
new Photos();
