<?php

	/**
	* Description : Custom Meta Box and Other functionality for the admin.
	* Package : Profile Directory Filter
	* Version : 1.0
	* Author : Glen Mongaya
	*/

	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	}

	// Hook meta box
	add_action('add_meta_boxes', 'pdfi_custom_meta_box');
	add_action('save_post','pdfi_save_meta_box_fields');
	add_filter('manage_'. $GLOBALS['PDFI']->post_type_name .'_posts_columns','pdfi_change_column_text');

	/**
	* Register Custom Meta Box
	*/

	function pdfi_custom_meta_box() {
		add_meta_box(
			'pdfi-meta-box',
			esc_html__( 'User Profile Details', 'pdfi-text' ),
			'pdfi_display_meta_box',
			$GLOBALS['PDFI']->post_type_name,
			'normal',
			'high'
		);
	}

	/**
	* Display Meta Box - Callback
	*/

	function pdfi_display_meta_box( $post, $metabox ) {

		// Get all meta values
		$name = get_post_meta( $post->ID, 'pdfi_firstname' , true);
		$lname = get_post_meta( $post->ID, 'pdfi_lastname' , true);
		$email = get_post_meta( $post->ID, 'pdfi_email' , true);
		$occupation = get_post_meta( $post->ID, 'pdfi_occupation' , true);
		$description = get_post_meta( $post->ID, 'pdfi_description' , true);

	?>
		<div class="pdfi_meta_wrapper">
			<div class="group-fields">
				<div class="one-half">
					<label><?php echo __('First Name','pdfi-text'); ?></label>
					<input type="text" class="input" value="<?php echo ( $name ? $name : '' ); ?>" name="pdfi_firstname">
				</div>
				<div class="one-half">
					<label><?php echo __('Last Name','pdfi-text'); ?></label>
					<input type="text" class="input" value="<?php echo ( $lname ? $lname : '' ); ?>" name="pdfi_lastname">
				</div>
			</div>
			<div class="group-fields">
				<div class="one-half">
					<label><?php echo __('Email Address','pdfi-text'); ?></label>
					<input type="text" class="input" value="<?php echo ( $email ? $email : '' ); ?>" name="pdfi_email">
				</div>
				<div class="one-half">
					<label><?php echo __('Occupation','pdfi-text'); ?></label>
					<input type="text" class="input" value="<?php echo ( $occupation ? $occupation : '' ); ?>" name="pdfi_occupation">
				</div>
			</div>
			<div class="group-fields">
				<div class="full">
					<label><?php echo __('Description','pdfi-text'); ?></label>					
					<?php
						$editor_id = 'pdfi_description';
						$settings = array(
							'editor_css'	=>	'<style>.wp-core-ui .button.button-small { width:auto; display:inline-block; }</style>'
						);
						wp_editor( $description, $editor_id, $settings );
					?>
				</div>
			</div>

			<?php
				// Nonce for security
				wp_nonce_field( 'pdf_save_meta_action', 'pdfi_meta_nonce' );
			?>

		</div>
	<?php
	}

	/**
	* Save Custom Post Meta - Fields From Meta Box
	*/

	function pdfi_save_meta_box_fields( $post_id ) {

		// Get post type
		$post_type = get_post_type($post_id);

		// Check For specific post_type only
		if ( $GLOBALS['PDFI']->post_type_name != $post_type ) {
			return;
		}

		// Check nonce if valid
		if(  isset( $_POST['pdfi_meta_nonce'] ) && ! wp_verify_nonce( $_POST['pdfi_meta_nonce'], 'pdf_save_meta_action' ) ) {
			return;
		}

		// Save all fields
		if( isset( $_POST['pdfi_firstname'] ) ) {
			update_post_meta( $post_id, 'pdfi_firstname', sanitize_text_field( $_POST['pdfi_firstname'] ) );
		}

		if( isset( $_POST['pdfi_lastname'] ) ) {
			update_post_meta( $post_id, 'pdfi_lastname', sanitize_text_field( $_POST['pdfi_lastname'] ) );
		}

		if( isset( $_POST['pdfi_lastname'] ) ) {
			update_post_meta( $post_id, 'pdfi_email', sanitize_email( $_POST['pdfi_email'] ) );
		}

		if( isset( $_POST['pdfi_occupation'] ) ) {
			update_post_meta( $post_id, 'pdfi_occupation', sanitize_text_field( $_POST['pdfi_occupation'] ) );
		}

		if( isset( $_POST['pdfi_description'] ) ) {
			update_post_meta( $post_id, 'pdfi_description', wp_kses_post( $_POST['pdfi_description'] ) );
		}
	}

	/**
	* Change Title column to Name
	*/

	function pdfi_change_column_text( $column ) {
		$column['title'] = __('Profile Name','pdfi-text');
		return $column;
	}