<?php

/**
 *
 * The plugin bootstrap file
 *
 * This file is responsible for starting the plugin using the main plugin class file.
 *
 * @since 0.0.1
 * @package Plugin_Name
 *
 * @wordpress-plugin
 * Plugin Name:     MC Citacion
 * Description:     Agregar citas a los artículos que ya existen
 * Version:         0.0.1
 * Author:          diurvan
 * Author URI:      https://www.example.com
 * License:         GPL-2.0+
 * License URI:     http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:     mc-citacion
 * Domain Path:     /lang
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Direct access not permitted.' );
}

if ( ! class_exists( 'mc_citacion' ) ) {

	/*
	 * main mc_citacion class
	 *
	 * @class mc_citacion
	 * @since 0.0.1
	 */
	class mc_citacion {

		/*
		 * mc_citacion plugin version
		 *
		 * @var string
		 */
		public $version = '4.7.5';

		/**
		 * The single instance of the class.
		 *
		 * @var mc_citacion
		 * @since 0.0.1
		 */
		protected static $instance = null;

		/**
		 * Main mc_citacion instance.
		 *
		 * @since 0.0.1
		 * @static
		 * @return mc_citacion - main instance.
		 */
		public static function instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		/**
		 * mc_citacion class constructor.
		 */
		public function __construct() {
			$this->load_plugin_textdomain();
			$this->define_constants();
			$this->includes();
			$this->define_actions();
		}

		public function load_plugin_textdomain() {
			load_plugin_textdomain( 'wc-citacion', false, basename( dirname( __FILE__ ) ) . '/lang/' );
		}

		/**
		 * Include required core files
		 */
		public function includes() {
			// Load custom functions and hooks
			require_once __DIR__ . '/includes/includes.php';
		}

		/**
		 * Get the plugin path.
		 *
		 * @return string
		 */
		public function plugin_path() {
			return untrailingslashit( plugin_dir_path( __FILE__ ) );
		}


		/**
		 * Define plugin_name constants
		 */
		private function define_constants() {
			define( 'MC_CITACION_PLUGIN_FILE', __FILE__ );
			define( 'MC_CITACION_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
			define( 'MC_CITACION_VERSION', $this->version );
			define( 'MC_CITACION_PATH', $this->plugin_path() );
		}

		/**
		 * Define mc_citacion actions
		 */
		public function define_actions() {
			//
		}

		/**
		 * Define plugin_name menus
		 */
		public function define_menus() {
            //
		}
	}

	$mc_citacion = new mc_citacion();
}
