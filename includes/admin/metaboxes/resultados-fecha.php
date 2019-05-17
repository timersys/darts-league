<?php
wp_nonce_field( 'dartsl_options', 'dartsl_options_nonce' );
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$partidos = [];
$is_liga = get_post_meta(get_the_id(), 'is_liga', false);

if( isset($torneo['comenzado']) ) {
	global $wpdb;
	// partidos
	$partidos = $wpdb->get_results( $wpdb->prepare( "SELECT *, user1.display_name as player1_name, user2.display_name as player2_name FROM {$wpdb->prefix}dartsl_matches LEFT JOIN $wpdb->users user1 ON user1.ID = player1_id LEFT JOIN $wpdb->users user2 ON user2.ID = player2_id WHERE fecha_id = %d", get_the_id() ) );
	// posiciones fecha
	$posiciones = $wpdb->get_results( $wpdb->prepare(
		"SELECT points, rank, jugador, SUM(Win) As ganados, SUM(Loss) as perdidos, SUM(Draw) as empatados, SUM(score) as lf, SUM(lc) as lc, AVG(darts_avg) as avg, MAX(co) as co, SUM(score) - SUM(lc) as dif, SUM(d180) as d180
FROM
( SELECT  dm.fecha_id, user1.display_name as jugador, rank, points,
     CASE WHEN player1_score > player2_score THEN 1 ELSE 0 END as Win, 
     CASE WHEN player1_score < player2_score THEN 1 ELSE 0 END as Loss, 
     CASE WHEN player1_score = player2_score THEN 1 ELSE 0 END as Draw, 
     player1_co AS co,
     player1_avg as darts_avg,
	 player1_score as score,
	 player1_180 as d180,
 player2_score as lc
  FROM {$wpdb->prefix}dartsl_matches dm
  LEFT JOIN {$wpdb->prefix}users user1 ON user1.ID = player1_id
  LEFT JOIN {$wpdb->prefix}dartsl_ranks r ON player1_id = r.user_id AND r.fecha_id = %d
  UNION ALL
  SELECT  dm.fecha_id, user2.display_name as jugador, r2.rank, r2.points,
     CASE WHEN player2_score > player1_score THEN 1 ELSE 0 END as Win, 
     CASE WHEN player2_score < player1_score THEN 1 ELSE 0 END as Loss, 
     CASE WHEN player2_score = player1_score THEN 1 ELSE 0 END as Draw, 
     player2_co AS co,
     player2_avg as darts_avg,
 	player2_score as score,
  player2_180 as d180,
  player1_score as lc
  FROM {$wpdb->prefix}dartsl_matches dm
  LEFT JOIN {$wpdb->prefix}users user2 ON user2.ID = player2_id
  LEFT JOIN {$wpdb->prefix}dartsl_ranks r2 ON player2_id = r2.user_id AND r2.fecha_id = %d
) t
  WHERE fecha_id = %d 
GROUP BY jugador
ORDER By rank, dif DESC", get_the_id(),get_the_id(),get_the_id()));

	$rankings = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}dartsl_ranks WHERE fecha_id = %d", get_the_id() ) );
}
?>
<table class="form-table">


	<tr>
		<th>
			<label>
				<?php if( empty($partidos) && isset($torneo['comenzado']) ) : ?>
					<button class="components-button is-button is-default is-primary is-small cargar_resultados">Cargar resultados</button>
				<?php endif ;?>
			</label>
		</th>

	<tr>
		<th>
			<label><?php _e( 'Partidos Jugados:', 'dartsl' ); ?></label>
		</th>
		<td>
			<table id="partidos">
				<?php
				if( !empty($partidos) ) {
					foreach ($partidos as $partido) {
						$partido = (array) $partido;

						echo '<tr>' .
						     '<td class="left_match">' .
						     '<input type="hidden" name="winner[]" value="'. $partido['winner'].'"/>' .
						     '<div class="match_name"><span>' . $partido['player1_name'] . '</span> <input type="hidden" name="player1_id[]" value="'. $partido['player1_id'].'"></div>' .
						     '<div class="match_avg">Avg <input type="number" step="0.01" name="player1_avg[]" value="' . $partido['player1_avg'] . '"></div>' .
						     '<div class="match_co">CO <input type="number" step="0.01" name="player1_co[]" value="' . $partido['player1_co'] . '"></div>' .
						     '<div class="match_180">180 <input type="number" step="1" name="player1_180[]" value="' . $partido['player1_180'] . '"></div>' .
						     '</td>' .
						     '<td><input type="text" class="player_score" name="player1_score[]" value="'. $partido['player1_score'].'" disabled><input type="hidden" name="player1_score[]" value="'. $partido['player1_score'].'"></td>' .
						     '<td><input type="text" class="player_score" name="player2_score[]" value="'. $partido['player2_score'].'" disabled><input type="hidden" name="player2_score[]" value="'. $partido['player2_score'].'"></td>' .
						     '<td class="right_match">' .
						     '<div class="match_name"><span>' . $partido['player2_name'] . '</span> <input type="hidden" name="player2_id[]" value="'. $partido['player2_id'].'"></div>' .
						     '<div class="match_avg"><input type="number" step="0.01" name="player2_avg[]" value="' . $partido['player2_avg'] . '"> Avg</div>' .
						     '<div class="match_co"><input type="number" step="0.01" name="player2_co[]" value="' . $partido['player2_co'] . '"> CO</div>' .
						     '<div class="match_180"><input type="number" step="1" name="player2_180[]" value="' . $partido['player2_180'] . '"> 180</div>' .
						     '</td>' .
						     '</tr>';
					}
				}
				?>
			</table>
			<div id="rankings">
				<?php
				if( !empty($rankings) ) {
					foreach ( $rankings as $ranking ) {
						echo '<input type="hidden" name="ranking['.$ranking->user_id.']" value="'.$ranking->rank.'">';
					}
				}
				?>
			</div>
		</td>
	</tr>
	<?php if (isset($torneo['comenzado'])) {?>
	<tr>
		<th>
			<label><?php _e( 'Posiciones fecha:', 'dartsl' ); ?></label>
		</th>
		<td>
			<table id="posiciones">
				<thead>
					<tr>
						<th>Nombre</th><th>G</th><th>E</th><th>P</th><th>LF</th><th>LC</th><th>Dif.</th><th>CO</th><th>AVG</th><th>Pts</th>
					</tr>
				</thead>
			<?php
				$opts = get_option('dartsl_settings');
				if( !empty($posiciones) ) {
					foreach ($posiciones as $i => $pos) {
						echo '<tr><td>'.$pos->jugador.'</td><td>'.$pos->ganados.'</td><td>'.$pos->empatados.'</td><td>'.$pos->perdidos.'</td><td>'.$pos->lf.'</td><td>'.$pos->lc.'</td><td>'.$pos->dif.'</td><td>'.$pos->co.'</td><td>'.number_format($pos->avg,2).'</td><td>'.$pos->d180.'</td><td>'.$pos->points.'</td></tr>';
					}
				}
			?>
			</table>
		</td>
	</tr>
	<?php } ?>

</table>