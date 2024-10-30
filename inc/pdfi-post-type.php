<?php

	/**
	* Description : Register Custom Post Type
	* Package : Profile Directory Filter
	* Version : 1.0
	* Author : Glen Mongaya
	*/

	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	}

	if( ! function_exists('pdfi_register_type_init') ) {
		function pdfi_register_type_init( $post_type_name = null ) {
			$labels = array(
				'name'               => _x( 'Profile Directory', 'post type general name', 'pdfi-text' ),
				'singular_name'      => _x( 'Profile', 'post type singular name', 'pdfi-text' ),
				'menu_name'          => _x( 'Profile Directory', 'admin menu', 'pdfi-text' ),
				'name_admin_bar'     => _x( 'Profile', 'add new on admin bar', 'pdfi-text' ),
				'add_new'            => _x( 'Add New', 'Profile', 'pdfi-text' ),
				'add_new_item'       => __( 'Add New Profile', 'pdfi-text' ),
				'new_item'           => __( 'New Profile', 'pdfi-text' ),
				'edit_item'          => __( 'Edit Profile', 'pdfi-text' ),
				'view_item'          => __( 'View Profile', 'pdfi-text' ),
				'all_items'          => __( 'All Profile', 'pdfi-text' ),
				'search_items'       => __( 'Search Profile Directory', 'pdfi-text' ),
				'parent_item_colon'  => __( 'Parent Profile Directory:', 'pdfi-text' ),
				'not_found'          => __( 'No Profile Directory found.', 'pdfi-text' ),
				'not_found_in_trash' => __( 'No Profile Directory found in Trash.', 'pdfi-text' )
			);

			$args = array(
				'labels'             => $labels,
				'public'             => false,
				'publicly_queryable' => false,
				'show_ui'            => true,
				'show_in_menu'       => true,
				'query_var'          => true,
				'capability_type'    => 'post',
				'has_archive'        => false,
				'hierarchical'       => false,
				'menu_position'      => null,
				'supports'           => array( 'title', 'thumbnail' )
			);

			register_post_type( $post_type_name, $args );
		}
	}