<?php

	/**
	* Description : A custom function for this plugin.
	* Package : Profile Directory Filter
	* Version : 1.0
	* Author : Glen Mongaya
	*/

	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	}

	// Ajax hooks
	add_action('wp_ajax_pdfi_get_user_profile','pdfi_get_user_info');
	add_action('wp_ajax_nopriv_pdfi_get_user_profile','pdfi_get_user_info');

	// Load More Acton Hooks
	add_action('wp_ajax_load_more_profile','pdfi_load_more');
	add_action('wp_ajax_nopriv_load_more_profile','pdfi_load_more');

	// Filter hooks
	add_filter('pdfi_profile_directory_class','_pdfi_add_column_class', 20, 3);

	// Generate opening div wrapper before the content
	function pdfi_before_content_wrap( $options ) {

		// If use bootstrap options is checked
		if( isset( $options['use_bootstrap'] ) && $options['use_bootstrap'] ) {
			return 'row column-'.esc_attr( $options['column'] );
		}

		// Default column container
		return 'pdfi-column column-'. esc_attr( $options['column'] );
	}

	// Add custom class on profile directory items
	function _pdfi_add_column_class( $classes, $class_name, $bootstrap ) {
		$settings = pdfi_settings();

		// If use bootstrap option is ticked.
		if( $bootstrap || isset( $settings['use_bootstrap'] ) && $settings['use_bootstrap'] ) {
			$column = ( 12 / (int) $settings['column'] );
			$classes[] = 'col-md-'. $column .' col-sm-' . $column . ' col-xs-12';
		}

		return $classes;
	}

	/**
	*  Function that will display profile lists
	*  Return : HTML
	*/

	function get_pdfi_profile_list( $atts, $paged = 1 ) {

		// Get options
		$options = pdfi_settings();

		// Setup arguments for custom query
		$args = array(
			'post_type'			=>	$GLOBALS['PDFI']->post_type_name,
			'posts_per_page'	=>	( isset( $atts['limit'] ) ? $atts['limit'] : $options['per_page'] ),
			'paged'				=>	$paged,
			'orderby'			=>	'meta_value',
			'meta_key'			=>	( $atts['filter_by'] == 'first_name' ? 'pdfi_firstname' : 'pdfi_lastname' ),
			'order'				=>	$options['order']
		);
		
		// Default content var
		$content = '';

		// Setup custom query
		$profile = new WP_Query( $args );

		// Begin Loop
		if( $profile->have_posts() ) :
			while( $profile->have_posts() ) : $profile->the_post();

				// Get current post ID
				$post_id = get_the_ID();

				// Get all post meta
				$data = pdfi_parse_meta( get_post_meta( $post_id ) );

				// Allow developers to add/modify post_meta before displaying to the template
				$data = apply_filters('pdfi_post_meta', $data, $post_id );

				// Pass ID
				$data['ID'] = $post_id;

				// Load template
				
				if( isset($atts['use_bootstrap']) && $atts['use_bootstrap'] == true || isset($options['use_bootstrap']) && $options['use_bootstrap'] == true ) {
					$content .= _pdfi_template( 'bootstrap-layout', $data );
				}else {
					$content .= _pdfi_template( 'default-layout', $data );
				}

			endwhile;
		endif;
		wp_reset_postdata();

		// Return total results number
		if( isset( $atts['count_posts'] ) ){
			return $profile->found_posts;
		}

		// Return html content
		return $content;
	}

	/**
	* Display and Generate profile filter from A-Z
	*/

	function get_pdfi_profile_filter( $filter_by = null ) {

		// Generate alphabet letters
		$filters =  range('A', 'Z');
		$html = '';

		$key_filter = ( $filter_by == 'first_name' ? 'pdfi_firstname' : 'pdfi_lastname' );

		// Wrap filter list
		$html = '<div class="pdfi-filter">';
			$html .= '<ul class="filter">';
				$html .= '<li><a href="" data-filter="*">'. __('All','pdfi-text') .'</a></li>';
				foreach( $filters as $filter_letter ) {
					if( pdfi_filter_exists( $filter_letter, $key_filter ) ) {
						$html .= '<li><a href="javascript:void(0)" data-filter="filter-'. strtolower( $filter_letter ) .'">'. __( $filter_letter, 'pdfi-text' ) .'</a></li>';
					}else {
						$html .= '<li class="disabled"><a href="javascript:void(0)" href="javascript:void(0)">'. __( $filter_letter, 'pdfi-text' ) .'</a></li>';
					}
				}
			$html .= '</ul>';
		$html .= '</div>';

		return $html;
	}

	/**
	* Parse Custom Post Meta
	*/

	function pdfi_parse_meta( $meta = array() ) {
		$meta_post = '';

		if( is_array( $meta ) ) {
			foreach( $meta as $index => $single_meta ) {
				$meta_post[$index] = $single_meta[0];
			}
		}

		return $meta_post;
	}

	/**
	* @description : Get plugin settings
	* @return : String
	*/

	function pdfi_settings( $option_name = null ) {
		$options = get_option('pdfi_options');
		if( $option_name && isset( $options[ $option_name ] ) )
			return $options[ $option_name ];
		else
			return $options;
	}

	/**
	* @description : Append class on Profile Item list.
	* @return : HTML attributes
	*/

	function pdfi_directory_class( $class_name = '', $data = array(), $use_bootstrap = false ) {

		//Default class name
		if( $class_name ) {
			$classes[] = $class_name;
		}

		// Add Profile directory ID
		$classes[] = 'profile-id-' . $data['ID'];

		// Add Filter Key First letter of First or Last name.
		$key_filter = ( pdfi_settings('filter_by') == 'first_name' ? 'pdfi_firstname' : 'pdfi_lastname' );

		if( isset( $data[ $key_filter ] ) &&  $data[ $key_filter ] ) {
			$letter = strtolower( substr( $data[ $key_filter ], 0,1 ) );
			$classes[] = 'filter-' . $letter;
		}

		// Allow filter classes
		$classes = apply_filters( 'pdfi_profile_directory_class', $classes, $class_name, $use_bootstrap );

		// Join classes
		echo 'class="'. join( ' ', array_unique( $classes ) ).'"';
	}

	/**
	* Check if there's any first letter match to our alphabet filter
	*/

	function pdfi_filter_exists( $letter, $filter_by ) {

		// Setup query arguments
		$args = array(
			'post_type'		=> $GLOBALS['PDFI']->post_type_name,
			'meta_query'	=>	array(
				array(
					'key'		=>	$filter_by,
					'value'		=>	'^'.$letter,
					'compare'	=>	'REGEXP'
				)
			)
		);

		// Query post
		$posts = get_posts( $args );

		// if we found posts then return true.
		if( $posts && count( $posts ) > 0 ) {
			return true;
		}

		return false;
	}

	/**
	* Custom function that will load php file - template
	*/

	function _pdfi_template( $template_name = null, $data = array() ) {

		// Template required
		if( ! $template_name )
			return;

		// Start to buffer
		ob_start();

		// Check if file exists
		if( file_exists( CDFI_PLUGIN_DIR . 'templates' . CDFI_DS . $template_name . '.php' ) ) {

			// Include / Load template
			include( CDFI_PLUGIN_DIR . 'templates' . CDFI_DS . $template_name . '.php' );

			// Return the current buffer contents.
			return ob_get_clean();
		}

		return false;
	}

	/**
	* Custom function that will display profile and resize image
	*/

	function the_pdfi_image( $thumbnail_id, $sizes = false ) {

		if( ! $thumbnail_id )
			return;

		// Get admin options
		$options = get_option('pdfi_options');

		// Custom Sizes
		if( $sizes && is_array( $sizes ) ) {
			$options['width'] = $sizes['width'];
			$options['height'] = $sizes['height'];
		}

		// Should we crop the image?
		$crop = ( ( isset( $options['crop_image'] ) && $options['crop_image'] ) ? true : false );

		// Get image src
		$profile_img = wp_get_attachment_image_src( $thumbnail_id, 'large' );

		// Resize image using aq_resize Script
		echo aq_resize( $profile_img[0], $options['width'], $options['height'], $crop );
	}

	/**
	* @description : Add custom modal for profile
	* @return : HTML
	*/

	add_filter('pdfi_after_profile_content', 'pdfi_append_modal_container', 10, 1);

	function pdfi_append_modal_container( $content ) {

		$modal = '';
		$modal .= '<div class="pdfi-modal" style="display:none;">';
			$modal .= '<div class="pdfi-modal-content">';
				$modal .= '<span class="close">&times;</span>';
				$modal .= '<div id="popup-content-wrap"></div>';
			$modal .= '</div>';
		$modal .= '</div>';

		return $content . $modal;
	}

	/**
	* @Description : Plugin Ajax Request - get user profile information
	* @return : JSON
	*/

	function pdfi_get_user_info() {

		// Check ajax security
		check_ajax_referer( 'pdfi-ajax-nonce', 'security' );

		// post profile ID
		$profile_id = intval( $_POST['id'] );

		// Get Profile Info by ID
		$posts = get_post( $profile_id, ARRAY_A );

		if( $posts ) {

			// Get all post meta
			$data = pdfi_parse_meta( get_post_meta( $posts['ID'] ) );

			// Get modal template template /templates/modal-content.php
			$profile_content = _pdfi_template('modal-content', array_merge( $posts, $data ));

			// Send json to ajax response
			echo wp_send_json_success( $profile_content );
		}

		die;
	}

	/**
	* @Description : Plugin Ajax Request - Load more profile
	* @return : JSON
	*/

	function pdfi_load_more() {

		// Check ajax security
		check_ajax_referer( 'pdfi-ajax-nonce', 'security' );

		// Get options
		$options = pdfi_settings();

		// Query profile
		$profile_content = get_pdfi_profile_list(
			array(
				'filter_by'		=>	( isset( $_POST['filter_by'] )  ? $_POST['filter_by'] : '' ),
				'limit' 		=> 	( isset( $_POST['per_page'] ) ? $_POST['per_page'] : $options['per_page'] ),
				'use_bootstrap'	=>	( isset( $options['use_bootstrap'] ) ? true : false )
			),
			intval( $_POST['page'] + 1 )
		);

		// Return json format Content
		if( $profile_content ) {
			echo wp_send_json_success( $profile_content );
		}else {
			echo wp_send_json_error( 'No more posts to show.' );
		}
	}