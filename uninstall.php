<?php

	// if uninstall.php is not called by WordPress, die
	if (!defined('WP_UNINSTALL_PLUGIN')) {
		die;
	}
	
	// Delete Options
	$option_name = 'pdfi_options';
	delete_option( $option_name );
	
	// Delete Post Type
	$posts = get_posts('post_type=pdfi-profile&posts_per_page=-1');
	foreach( $posts as $post ) {
		// Delete profile filter post type
		wp_delete_post( $post->ID, true );
		
		// Get all post meta and delete
		$post_meta = get_post_meta( $post->ID );
		foreach( $post_meta as $key => $meta ) {
			delete_post_meta( $post->ID, $key );
		}
	}
	
	// Clear any cached data that has been removed
	wp_cache_flush();