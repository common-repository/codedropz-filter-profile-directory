<?php

	/**
	* Description : A shortcode that display user profile directory lists.
	* Package : Profile Directory Filter
	* Version : 1.0
	* Author : Glen Mongaya
	*/

	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	}

	add_shortcode('pdfi_profile_directory', 'pdfi_show_profile_directory');

	if( ! function_exists('pdfi_show_profile_directory') ) {

		// Function shortcode callback - displaying profile
		function pdfi_show_profile_directory( $atts, $content = null ) {

			// Get plugin options
			$options = pdfi_settings();

			// Default attributes
			$defaults = array(
				'limit'				=>	( isset( $options['per_page'] ) ? $options['per_page'] : 10 ),
				'show_readmore'		=>	true,
				'column'			=>	( isset( $options['column'] ) ? $options['column'] : 4 ),
				'use_bootstrap'		=>	( isset( $options['use_bootstrap'] ) ? true : false ),
				'filter_by'			=>	( isset( $options['filter_by'] ) ? $options['filter_by'] : '' )
			);			
			
			//Combine user shortcode attributes
			$atts = shortcode_atts( $defaults, $atts );
			
			// Add options via javascripts
			wp_localize_script('x-pdfi', 'pdfi_options', $atts );
			
			// Concat content and display
			$content = '';

			// Add filter before profile content.
			$content = apply_filters('pdfi_before_profile_content', $content );

			// Container Wrapper
			$content .= '<div class="pdfi-profile-wrapper">';

				// Filter
				$content .= get_pdfi_profile_filter( $options['filter_by'] );

				// Before Content - Wrap
				$content .= '<div data-id="p-filter-content" class="'. pdfi_before_content_wrap( $atts ) .'">';

					// Profile Content
					$content .= get_pdfi_profile_list( $atts );

				// After content - Wrap for closin tag
				$content .= '</div>';

				// Get Total Items
				$atts['count_posts'] = true;
				$total_items = get_pdfi_profile_list( $atts );

				// Load More Button
				if( $total_items > $atts['limit'] && $atts['show_readmore'] ) {
					$content .= '<div class="pdfi-load-more">';
						$content .= '<a href="javascript:void(0)" class="btn" data-action="load-more">'.__('Load More','pdfi-text').'</a>';
					$content .= '</div>';
				}

			$content .= '</div>';

			// Add filter before displaying profile content.
			$content = apply_filters('pdfi_after_profile_content', $content );

			return $content;
		}

	}