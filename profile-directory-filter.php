<?php

	/**
	* Plugin Name: Profile Directory - With Filter
	* Plugin URI: http://codedropz.com
	* Description: A simple plugin that has capability to filter (Ex : A-Z) on users Firstname or Lastname.
	* Version: 1.0.1
	* Author: Glen Don L. Mongaya
	* Author URI: http://codedropz.com
	* License: GPL2
	*/

	/**  This protect the plugin file from direct access */
	if ( ! defined( 'WPINC' ) ) {
		die;
	}

	/**  Define our Plugin Version */
	define( 'CDFI_VERSION', '1.0' );

	/* Directory Separator */
	define( 'CDFI_DS', DIRECTORY_SEPARATOR );

	/**  Define our constant Plugin Name */
	define( 'CDFI_PLUGIN_NAME', trim( dirname( plugin_basename( __FILE__ ) ) ) );

	/**  Define our constant Plugin Directories : ex : c:plugin_path/my_plugin */
	define( 'CDFI_PLUGIN_DIR', untrailingslashit( dirname( __FILE__ ) ) . CDFI_DS );

	/**  Define our constant Plugin URL */
	define( 'CDFI_PLUGIN_URL', untrailingslashit( plugins_url( CDFI_PLUGIN_NAME ) ) );

	// Begin execution : Load custom configuration, modules and settings
	require_once CDFI_PLUGIN_DIR . CDFI_DS . 'inc/pdfi_init.php';
	
	// On plugin activation
	register_activation_hook( __FILE__ , array( 'Codedropz_Profile_dir_Filter', 'PDFI_install' ) );
