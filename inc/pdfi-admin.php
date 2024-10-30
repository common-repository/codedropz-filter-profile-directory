<?php

	/**
	* Description : File that will display Admin Settings
	* Package : Profile Directory Filter
	* Version : 1.0
	* Author : Glen Mongaya
	*/

	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	}

	// Wordpress admin action hooks
	add_action('admin_menu', 'pdfi_admin_sub_menu');
	add_action('admin_init','pdfi_settings_init');

	/**
	* Register Settings Menu - "Under Profile Directory Post Type"
	*/

	function pdfi_admin_sub_menu() {
		$parent_slug = 'edit.php?post_type=' . $GLOBALS['PDFI']->post_type_name;
		add_submenu_page( $parent_slug , 'Profile Directory - Settings', 'Settings', 'manage_options', 'pdfi-settings', 'pdfi_admin_display_settings');
	}

	/**
	* Display Submenu - Callback
	*/

	function pdfi_admin_display_settings() {
	?>
		<div class="wrap">

			<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>

			<?php
				// Error Settings
				settings_errors();

				// Get options
				$options = get_option('pdfi_options');
			?>

			 <form action="options.php" method="post">

				<?php
					settings_fields( 'pdfi-dir-options' );
					do_settings_sections( 'pdfi-dir-options' );
				?>

				<table class="form-table pdfi-table">
					<tr valign="top">
						<th scope="row"><?php echo __('Profile Show Per Page','pdfi-text'); ?></th>
						<td>
							<input class="regular-text" type="text" name="pdfi_options[per_page]" value="<?php echo ( isset($options['per_page']) ? $options['per_page'] : '' ); ?>" />
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php echo __('Filter Profile By','pdfi-text'); ?></th>
						<td>
							<select name="pdfi_options[filter_by]" style="min-width:25em;">
								<option <?php selected( $options['filter_by'], 'first_name' ); ?> value="first_name">First Name</option>
								<option <?php selected( $options['filter_by'], 'last_name' ); ?> value="last_name">Last Name</option>
							</select>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php echo __('Order','pdfi-text'); ?></th>
						<td>
							<select name="pdfi_options[order]" style="min-width:25em;">
								<option <?php selected( $options['order'], 'ASC' ); ?> value="ASC">ASC</option>
								<option <?php selected( $options['order'], 'DESC' ); ?> value="DESC">DESC</option>
							</select>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php echo __('Column Layout','pdfi-text'); ?></th>
						<td><input class="regular-text" type="text" name="pdfi_options[column]" value="<?php echo ( isset($options['column']) ? $options['column'] : '' ); ?>" /></td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php echo __('Image Size','pdfi-text'); ?></th>
						<td>
							<input type="text" placeholder="Width" class="small" name="pdfi_options[width]" value="<?php echo ( isset($options['width']) ? absint($options['width']) : '' ); ?>" /> <input placeholder="Height" class="small" type="text" name="pdfi_options[height]" value="<?php echo ( isset($options['height']) ? absint($options['height']) : '' ); ?>" />
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php echo __('Crop Image','pdfi-text'); ?></th>
						<td>
							<label>
								<input name="pdfi_options[crop_image]" <?php ( isset( $options['crop_image'] ) ? checked( $options['crop_image'], 1 ) : '' ); ?> type="checkbox" value="1">Yes
							</label>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php echo __('Bootstrap','pdfi-text'); ?></th>
						<td>
							<label>
								<input name="pdfi_options[use_bootstrap]" <?php ( isset( $options['use_bootstrap'] ) ? checked( $options['use_bootstrap'], 1 ) : '' ); ?> type="checkbox" value="1">
								<em>Yes</em>
								<p class="description">Check this option if you want to use Bootstrap Grid layout (Please include Bootstrap file to your Theme).</p>
							</label>
						</td>
					</tr>
				</table>

				<?php
					// Submit Button
					submit_button('Save Settings');
				?>
			</form>
		</div>
	<?php
	}

	/**
	* Register Custom Settings
	*/

	function pdfi_settings_init() {
		// Group name, Option Name
		register_setting( 'pdfi-dir-options', 'pdfi_options' );
	}