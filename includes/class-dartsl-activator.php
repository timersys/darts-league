<?php

/**
 * Fired during plugin activation
 *
 * @link       https://timersys.com
 * @since      1.0.0
 *
 * @package    DartsL
 * @subpackage DartsL/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    DartsL
 * @subpackage DartsL/includes
 * @author     Damian Logghe <damian@timersys.com>
 */
class DartsL_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		global $wpdb;

		$fechas_table = "CREATE TABLE IF NOT EXISTS `{$wpdb->base_prefix}dartsl_matches` (
		`id`	 		INT(6) UNSIGNED NOT NULL AUTO_INCREMENT, -- the id just for numeric
		`torneo_id` 	INT(6) UNSIGNED NOT NULL, -- torneo post id
		`fecha_id` 	INT(6) UNSIGNED NOT NULL, -- fecha post id
		`winner` 	INT(6) UNSIGNED NOT NULL, -- winner user id
		`player1_id` 	INT(6) UNSIGNED NOT NULL, -- player 1 id
		`player1_score` 	INT(6) UNSIGNED NOT NULL, -- player match score
		`player1_avg` 	FLOAT(6,2) UNSIGNED NOT NULL, -- player 1 match avg
		`player1_co` 	INT(6) UNSIGNED NOT NULL, -- player 1 match co
		`player2_id` 	INT(6) UNSIGNED NOT NULL, -- player 1 id
		`player2_score` 	INT(6) UNSIGNED NOT NULL, -- player match score
		`player2_avg` 	FLOAT(6,2) UNSIGNED NOT NULL, -- player 1 match avg
		`player2_co` 	INT(6) UNSIGNED NOT NULL, -- player 1 match co
		PRIMARY KEY( `id`),
        INDEX (torneo_id, fecha_id, winner),
        INDEX (player1_id, player2_id)
		) DEFAULT CHARSET=UTF8 COLLATE=UTF8_GENERAL_CI AUTO_INCREMENT=1 ;";

		$ranking_table = "CREATE TABLE IF NOT EXISTS `{$wpdb->base_prefix}dartsl_ranks` (
		`id`	 		INT(6) UNSIGNED NOT NULL AUTO_INCREMENT, -- the id just for numeric
		`torneo_id` 	INT(6) UNSIGNED NOT NULL, -- torneo post id
		`fecha_id` 	INT(6) UNSIGNED NOT NULL, -- fecha post id
		`user_id` 	INT(6) UNSIGNED NOT NULL, --  user id
		`rank` 	INT(6) UNSIGNED NOT NULL, -- ranking for fecha
		PRIMARY KEY( `id`),
        INDEX (torneo_id, fecha_id, user_id)
		) DEFAULT CHARSET=UTF8 COLLATE=UTF8_GENERAL_CI AUTO_INCREMENT=1 ;";


		$table_name = "{$wpdb->base_prefix}dartsl_matches";
		$ranks_table_name = "{$wpdb->base_prefix}dartsl_ranks";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );


		try {
			if ( $wpdb->get_var( "SHOW TABLES LIKE '{$table_name}'" ) != $table_name )
				dbDelta( $fechas_table  );
			if ( $wpdb->get_var( "SHOW TABLES LIKE '{$ranks_table_name}'" ) != $ranks_table_name )
				dbDelta(  $ranking_table );
		}catch (Exception $e){
			echo '<pre>';
			var_dump($e->getMessage());
			echo '</pre>';
			die();
		}

		do_action('dartsl_activated');
	}

}
