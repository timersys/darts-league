<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the dashboard.
 *
 * @link       https://timersys.com
 * @since      1.0.0
 *
 * @package    DartsL
 * @subpackage DartsL/includes
 */


/**
 * The core plugin class.
 *
 * This is used to define internationalization, dashboard-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    DartsL
 * @subpackage DartsL/includes
 * @author     Your Name <email@example.com>
 */
class DartsL {

	/**
	 * @var DartsL_Admin $admin
	 */
	public $admin;
	/**
	 * @var DartsL_Settings $settings
	 */
	public $settings;


	/**
	 * Plugin Instance
	 * @since 1.0.0
	 * @var The DartsL plugin instance
	 */
	protected static $_instance = null;

	/**
	 * Main DartsL Instance
	 *
	 * Ensures only one instance of WSI is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @see DARTSL()
	 * @return DartsL
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Cloning is forbidden.
	 * @since 1.0.0
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'dartsl' ), '2.1' );
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 * @since 1.0.0
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'dartsl' ), '2.1' );
	}


	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the Dashboard and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->load_dependencies();
		$this->set_locale();
		$this->set_objects();
		$this->register_scripts();
	}

	/**
	 * Load the required dependencies for this plugin.
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-dartsl-i18n.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/global/class-dartsl-cpt.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/global/class-dartsl-fecha-cpt.php';
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the DartsL_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new DartsL_i18n();
		$plugin_i18n->set_domain( 'dartsl' );

		add_action( 'plugins_loaded', [ $plugin_i18n, 'load_plugin_textdomain' ] );

	}

	/**
	 * Set all global objects
	 */
	private function set_objects() {
		#$this->admin    = new DartsL_Admin();
		#$this->settings = new DartsL_Settings();
	}

	/**
	 * Register scripts and styles for later usage
	 */
	private function register_scripts() {
		// scripts
		wp_register_script( 'dartsl-selectize', DARTSL_PLUGIN_URL . 'includes/assets/js/selectize.min.js', ['jquery'], '0.12.6', true );

		// styles
		wp_register_style( 'dartsl-selectize', DARTSL_PLUGIN_URL . 'includes/assets/css/selectize.default.css', '0.12.6' );
	}

}
