<?php
wp_nonce_field( 'dartsl_options', 'dartsl_options_nonce' );
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$partidos = [];



global $wpdb;
// partidos
$partidos = $wpdb->get_results( $wpdb->prepare( "SELECT *, user1.display_name as player1_name, user2.display_name as player2_name FROM {$wpdb->prefix}dartsl_matches LEFT JOIN $wpdb->users user1 ON user1.ID = player1_id LEFT JOIN $wpdb->users user2 ON user2.ID = player2_id WHERE fecha_id = %d", get_the_id() ) );

?>
<table class="form-table">
	<tr>
		<td>
			<table id="partidos">
				<?php
				if( !empty($partidos) ) {
					foreach ($partidos as $partido) {
						$partido = (array) $partido;

						echo '<tr><td>Ganador <select name="winner[]">
								<option value="'. $partido['player1_id'].'" '.selected($partido['winner'], $partido['player1_id']).'>' . $partido['player1_name'] . '</option>
								<option value="'. $partido['player2_id'].'" '.selected($partido['winner'], $partido['player2_id']).'>' . $partido['player2_name'] . '</option>'.
						     '</select></td></tr>';
						echo '<tr>' .
						     '<td class="left_match">' .
						     '<div class="match_name"><span>' . $partido['player1_name'] . '</span> <input type="hidden" name="player1_id[]" value="'. $partido['player1_id'].'"></div>' .
						     '<div class="match_avg">Avg <input type="number" step="0.01" name="player1_avg[]" value="' . $partido['player1_avg'] . '"></div>' .
						     '<div class="match_co">CO <input type="number" step="0.01" name="player1_co[]" value="' . $partido['player1_co'] . '"></div>' .
						     '</td>' .
						     '<td><input type="text" class="player_score" name="player1_score[]" value="'. $partido['player1_score'].'"></td>' .
						     '<td><input type="text" class="player_score" name="player2_score[]" value="'. $partido['player2_score'].'"></td>' .
						     '<td class="right_match">' .
						     '<div class="match_name"><span>' . $partido['player2_name'] . '</span> <input type="hidden" name="player2_id[]" value="'. $partido['player2_id'].'"></div>' .
						     '<div class="match_avg"><input type="number" step="0.01" name="player2_avg[]" value="' . $partido['player2_avg'] . '"> Avg</div>' .
						     '<div class="match_co"><input type="number" step="0.01" name="player2_co[]" value="' . $partido['player2_co'] . '"> CO</div>' .
						     '</td>' .
						     '</tr>';
					}
				}
				?>
			</table>

		</td>
	</tr>
</table>