<?php

/*
Plugin Name: Darts League
Plugin URI: http://URI_Of_Page_Describing_Plugin_and_Updates
Description: Ligar argentina y torneo DARDOS Y BIRRRAAA.
Version: 1.0.2
Author: chifli
Author URI: https://timersys.com
License: A "Slug" license name e.g. GPL2
*/

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'DARTSL_PLUGIN_FILE', __FILE__ );
define( 'DARTSL_VERSION', '1.0.2' );
define( 'DARTSL_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'DARTSL_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'DARTSL_PLUGIN_HOOK', basename( dirname( __FILE__ ) ) . '/' . basename( __FILE__ ) );

/**
 * The core plugin class that is used to define internationalization,
 * dashboard-specific hooks, and public-facing site hooks.
 */
require_once plugin_dir_path( __FILE__ ) . 'includes/class-dartsl.php';

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-dartsl-activator.php
 */
function activate_dartsl() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-dartsl-activator.php';
	DartsL_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-dartsl-deactivator.php
 */
function deactivate_dartsl() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-dartsl-deactivator.php';
	DartsL_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_dartsl' );
register_deactivation_hook( __FILE__, 'deactivate_dartsl' );


DartsL::instance();


