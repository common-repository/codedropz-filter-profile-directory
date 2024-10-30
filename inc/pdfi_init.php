<?php

	/**
	* Description : Base loader that will load php files, hooks and filters
	* Package : Profile Directory Filter
	* Version : 1.0
	* Author : Glen Mongaya
	*/

	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	}

	if( ! class_exists('Codedropz_Profile_dir_Filter') ) {

		// Begin class
		class Codedropz_Profile_dir_Filter {

			public $post_type_name = null;

			private static $instance = null;

			/**
			* Creates or returns an instance of this class.
			*
			* @return  Init A single instance of this class.
			*/

			public static function get_instance() {
				if( null == self::$instance ) {
					self::$instance = new self;
				}
				return self::$instance;
			}

			/**
			* Load and initialize plugin
			*/

			private function __construct() {

				// Default Post Type name
				$this->post_type_name = apply_filters( 'pdfi_post_type_name', 'pdfi-profile' );

				// Load and setup our Plugin
				add_action('init', array( $this, 'init') );

				// Load Front End script
				add_action( 'wp_enqueue_scripts', array( $this , 'plugin_script') );

				// Load Admin script
				add_action( 'admin_enqueue_scripts', array( $this , 'admin_script') );

			}

			/**
			* Setup plugin and load files, assets, filter and administration functions
			*/

			public function init() {

				// Include files needed
				include_once( CDFI_PLUGIN_DIR . 'inc' . CDFI_DS . 'aq_resizer.php' );
				include_once( CDFI_PLUGIN_DIR . 'inc' . CDFI_DS . 'pdfi-post-type.php' );
				include_once( CDFI_PLUGIN_DIR . 'inc' . CDFI_DS . 'pdfi-custom-functions.php' );

				// Register Custom Post Type
				pdfi_register_type_init( $this->post_type_name );

				// Load files wheather if it's admin or front-end
				if( is_admin() ) {
					include_once( CDFI_PLUGIN_DIR . 'inc' . CDFI_DS . 'pdfi-admin.php' );
					include_once( CDFI_PLUGIN_DIR . 'inc' . CDFI_DS . 'pdfi-admin-meta-box.php' );
				}else {
					include_once( CDFI_PLUGIN_DIR . 'inc' . CDFI_DS . 'shortcode.php' );
				}
			}

			/**
			* Plugin activation
			*/

			public static function PDFI_install() {
				$options = array(
					'per_page'		=>	30,
					'filter_by'		=>	'first_name',
					'order'			=>	'ASC',
					'column'		=>	3,
					'width'			=>	400,
					'height'		=>	300,
					'crop_image'	=>	1
				);
				add_option('pdfi_options', $options );
			}


			/**
			* Proper way to enqueue scripts and style for this plugin
			*/

			public function plugin_script() {
				wp_enqueue_style( 'x-pdfi', CDFI_PLUGIN_URL . '/css/pdfi_style.css?v='.time() );
				wp_enqueue_script( 'x-pdfi', CDFI_PLUGIN_URL . '/js/pdfi_script.js?v='.time(), array('jquery'), '1.0.0', true );
				wp_localize_script( 'x-pdfi', 'pdfi_ojbect',  array( 'ajax_url' => admin_url( 'admin-ajax.php' ), 'security' => wp_create_nonce( 'pdfi-ajax-nonce' ), 'plugin_url' => plugins_url('', dirname( __FILE__ ) )));
			}

			/**
			* Load admin scripts
			*/

			public function admin_script( $hook ) {

				// Get current admin screen
				$screen = get_current_screen();

				// Return if current hook != to edit.php
				if ( 'edit.php' != $hook && $screen->post_type != $this->post_type_name ) {
					return;
				}

				wp_enqueue_style( 'pdfi-admin', CDFI_PLUGIN_URL . '/css/admin/pdfi_admin.css');
			}
		}
	}

	/**
	* Initialize using singleton pattern
	*/

	function PDFI_register() {
		return Codedropz_Profile_dir_Filter::get_instance();
	}

	// Launch the whole plugin.
	$GLOBALS[ 'PDFI' ] = PDFI_register();