<?php

/**
 * Fired during plugin deactivation
 *
 * @link       https://timersys.com
 * @since      1.0.0
 *
 * @package    DartsL
 * @subpackage DartsL/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    DartsL
 * @subpackage DartsL/includes
 * @author     Damian Logghe <damian@timersys.com>
 */
class DartsL_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {
		do_action('dartsl_deactivated');
	}

}
