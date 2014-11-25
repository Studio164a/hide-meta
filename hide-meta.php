<?php
/**
 * Plugin Name:			Hide Meta
 * Plugin URI:			https://github.com/Studio164a/hide-meta
 * Description:			Allows you to hide the meta relating to a specific post or page. Themes have to explicitly add support for the feature.
 * Author:				Studio 164a
 * Author URI:			http://164a.com
 * Version:     		1.0.0
 * Text Domain: 		hide-meta
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Start plugin. 
 *
 * Before doing anything, this checks whether the current theme supports the plugin. If not, nothing else is executed.
 *
 * This funtion is called on the after_setup_theme hook to ensure that themes have finished registering their
 * support by then.
 *
 * @return 	void
 * @since 	1.0.0
 */
if ( ! function_exists( 'hide_meta_start' ) ) :

	function hide_meta_start() {
		if ( false === get_theme_support( 'hide-meta' ) ) {
			return;
		}

		// Include admin class.
		if ( is_admin() ) {
			require_once( 'class-hide-meta-admin.php' );

			add_action( 'hide_meta_start', array( 'Hide_Meta_Admin', 'start' ) );
		}

		do_action( 'hide_meta_start' );
	}

	add_action( 'after_setup_theme', 'hide_meta_start', 99999 );

endif;

/**
 * A helper function to determine whether the current post should have the meta displayed.
 *
 * @param 	WP_Post 	$post 		Optional. If a post is not passed, the current $post object will be used.
 * @return 	boolean
 * @since 	1.0.0
 */
if ( ! function_exists( 'hide_post_meta' ) ) :

	function hide_post_meta( $post = '' ) {
		if ( ! strlen( $post ) ) {
			global $post;
		}

		return get_post_meta( $post->ID, '_hide_meta', true );
	}

endif;