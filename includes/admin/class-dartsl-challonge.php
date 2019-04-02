<?php

/**
 * Class DartsL_Challonge
 */
class DartsL_Challonge {
	private $challonge;

	/**
	 * DartsL_Challonge constructor.
	 */
	public function __construct() {
		require_once (DARTSL_PLUGIN_DIR . 'vendor/autoload.php');
		require_once (DARTSL_PLUGIN_DIR . 'includes/admin/class-challonge-wrapper.php');
		$opts = get_option('dartsl_settings');
		if( !empty($opts['api_key']) ) {
			$this->challonge = new Challonge_Wrapper( $opts['api_key'] );
		}
		add_action( 'wp_ajax_generar_llave', [ $this, 'ajax_generar_llave'] );
		add_action( 'wp_ajax_comenzar_torneo', [ $this, 'ajax_comenzar_torneo'] );
		add_action( 'wp_ajax_cargar_resultados', [ $this, 'ajax_cargar_resultados'] );
		add_action( 'wp_ajax_obtener_datos_existentes', [ $this, 'ajax_obtener_datos_existentes'] );
	}

	/**
	 * Ajax handler para generar llave
	 */
	public function ajax_generar_llave() {;
		$post_id = $_POST['post_id'];

		if( empty($post_id) ){
			die();
		}
		$darts_data = get_post_meta( $post_id, 'dartls_torneo', true);

		if( empty($_POST['participantes']) ){
			echo json_encode(['error' => 'Al menos tiene que haber 3 participantes']);
			wp_die();
		}

		if( empty($_POST['torneo_id']) ){
			echo json_encode(['error' => 'A que torneo pertenece la fecha']);
			wp_die();
		}
		$torneo = [
			'torneo' => filter_input( INPUT_POST, 'torneo_id', FILTER_VALIDATE_INT ),
			'participantes' => filter_input( INPUT_POST, 'participantes', FILTER_VALIDATE_INT, FILTER_REQUIRE_ARRAY )
		];

		update_post_meta( $post_id, 'dartls_fecha', $torneo );

		try {
			if( empty( $darts_data['challonge_url'] ) ) {
			$darts_data = [];
			// creamos torneo
			$tournament_create = $this->challonge->createTournament(
				[
					'tournament' => [
						'name'            => filter_input( INPUT_POST, 'title' ),
						'url'             => str_replace( '-', '_', sanitize_title( filter_input( INPUT_POST, 'title' ) . '_' . date( 'Y_m_d' ) ) ),
						'tournament_type' => 'double elimination',
						'notify_users_when_matches_open' => false,
						'notify_users_when_the_tournament_ends' => false,
					]
				] );

				if ( ! isset( $tournament_create->tournament ) || ! isset( $tournament_create->tournament->id ) ) {
					echo json_encode( [ 'error' => $tournament_create->error ] );
					wp_die();
				}
				// si llegamos aca actualizamos url
				$darts_data['challonge_url'] = $tournament_create->tournament->url;
				$darts_data['challonge_tournament_id'] = $tournament_create->tournament->id;

				update_post_meta( $post_id, 'dartls_torneo', $darts_data );
			}

			$usuarios = $this->generar_usuarios($torneo['participantes']);

			// reseteamos el torneo y agregamos jugadores
			$tournament_reset = $this->challonge->clearParticipants( $darts_data['challonge_tournament_id'] );
			if( isset($tournament_reset->error) ) {
				echo json_encode( [ 'error' => $tournament_reset->error ] );
				wp_die();
			}
			// añadimos partipantes
			$tournament_add = $this->challonge->createParticipantBulk( $darts_data['challonge_tournament_id'], [ 'participants' => $usuarios ] );
			if( isset($tournament_add->error) ) {
				echo json_encode( [ 'error' => $tournament_add->error ] );
				wp_die();
			}
			// randomize
			$randomize = $this->challonge->randomizeParticipants($darts_data['challonge_tournament_id']);
			if( isset($randomize->error) ) {
				echo json_encode( [ 'error' => $randomize->error ] );
				wp_die();
			}

			echo json_encode( [ 'success' => $darts_data['challonge_url'] ] );


			wp_die();
		}catch (Exception $e) {
			echo json_encode( [ 'error' => $e->getMessage() ] );
			wp_die();
		}

	}

	/**
	 * Ajax handler para comenzar torneo
	 */
	public function ajax_comenzar_torneo() {
		$post_id = $_POST['post_id'];

		if( empty($post_id) ){
			die();
		}
		$torneo = get_post_meta( $post_id, 'dartls_torneo', true);

		if( empty($torneo['challonge_tournament_id']) ){
			echo json_encode(['error' => 'No se puede seleccionar el torneo de challonge']);
			wp_die();
		}
		try {
			// empezamos el torneo
			$tournament_started = $this->challonge->startTournament($torneo['challonge_tournament_id']);
			if( isset($tournament_started->error) ) {
				echo json_encode( [ 'error' => $tournament_started->error ] );
				wp_die();
			}
			$torneo['comenzado'] = true;
			update_post_meta( $post_id, 'dartls_torneo', $torneo);
			echo json_encode( [ 'success' => 1 ] );
			wp_die();
		}catch (Exception $e) {
			echo json_encode( [ 'error' => $e->getMessage() ] );
			wp_die();
		}
	}

	public function ajax_cargar_resultados() {
		$post_id = $_POST['post_id'];

		if( empty($post_id) ){
			die();
		}
		$torneo = get_post_meta( $post_id, 'dartls_torneo', true);

		if( empty($torneo['challonge_tournament_id']) ){
			echo json_encode(['error' => 'No se puede seleccionar el torneo de challonge']);
			wp_die();
		}
		try {

			$resultados = $this->challonge->getTorneo($torneo['challonge_tournament_id'], ['include_matches' => 1, 'include_participants' => '1']);

			if( isset($resultados->error) ) {
				echo json_encode( [ 'error' => $resultados->error ] );
				wp_die();
			}
			if( isset($resultados->tournament) && is_null($resultados->tournament->completed_at) ) {
				echo json_encode( [ 'error' => 'El torneo no ha finalizado aun'] );
				wp_die();
			}
			$matches = [];
			if( isset($resultados->tournament->matches) && isset($resultados->tournament->participants) ) {
				foreach ($resultados->tournament->matches as $match) {
					$player1 = $this->findParticipant( $match->match->player1_id, $resultados->tournament->participants)[0];
					$player2 = $this->findParticipant( $match->match->player2_id, $resultados->tournament->participants)[0];

					$matches[] = [
						'player1_id' => $player1->participant->misc,
						'player1_challonge_id' => $player1->participant->id,
						'player1_name' => $player1->participant->name,
						'player1_score' => strstr($match->match->scores_csv, '-', true),
						'player2_id' => $player2->participant->misc,
						'player2_challonge_id' => $player2->participant->id,
						'player2_name' => $player2->participant->name,
						'player2_score' => trim(strstr($match->match->scores_csv, '-', false),'-'),
						'winner'  => $match->match->player1_id == $match->match->winner_id ?  $player1->participant->misc :  $player2->participant->misc
					];
				}
			}
			usort($resultados->tournament->participants, function ($a,$b) {
					return $a->participant->final_rank <> $b->participant->final_rank;
			});

			echo json_encode( [ 'success' => ['matches' => $matches, 'participants' => $resultados->tournament->participants] ] );
			wp_die();
		}catch (Exception $e) {
			echo json_encode( [ 'error' => $e->getMessage() ] );
			wp_die();
		}
	}
	public function ajax_obtener_datos_existentes() {
		$post_id = $_POST['post_id'];
		$url = $_POST['url'];

		if( empty($post_id) || empty($url) ){
			die();
		}

		try {

			$resultados = $this->challonge->getTorneo(str_replace('https://challonge.com/','', $url), ['include_matches' => 1, 'include_participants' => '1']);

			if( isset($resultados->error) ) {
				echo json_encode( [ 'error' => $resultados->error ] );
				wp_die();
			}
			if( isset($resultados->tournament) && is_null($resultados->tournament->completed_at) ) {
				echo json_encode( [ 'error' => 'El torneo no ha finalizado aun'] );
				wp_die();
			}
			$matches = [];
			if( isset($resultados->tournament->matches) && isset($resultados->tournament->participants) ) {
				foreach ($resultados->tournament->matches as $match) {
					$player1 = $this->findParticipant( $match->match->player1_id, $resultados->tournament->participants)[0];
					$player2 = $this->findParticipant( $match->match->player2_id, $resultados->tournament->participants)[0];

					$matches[] = [
						'player1_id' => $player1->participant->misc,
						'player1_challonge_id' => $player1->participant->id,
						'player1_name' => $player1->participant->name,
						'player1_score' => strstr($match->match->scores_csv, '-', true),
						'player2_id' => $player2->participant->misc,
						'player2_challonge_id' => $player2->participant->id,
						'player2_name' => $player2->participant->name,
						'player2_score' => trim(strstr($match->match->scores_csv, '-', false),'-'),
						'winner'  => $match->match->player1_id == $match->match->winner_id ?  'player1' :  'player2'
					];
				}
			}
			usort($resultados->tournament->participants, function ($a,$b) {
					return $a->participant->final_rank <> $b->participant->final_rank;
			});

			$torneo['comenzado'] = true;
			$torneo['challonge_url'] = $resultados->tournament->url;
			$torneo['challonge_tournament_id'] = $resultados->tournament->id;
			update_post_meta( $post_id, 'dartls_torneo', $torneo);

			echo json_encode( [ 'success' => ['matches' => $matches, 'participants' => $resultados->tournament->participants] ] );
			wp_die();
		}catch (Exception $e) {
			echo json_encode( [ 'error' => $e->getMessage() ] );
			wp_die();
		}
	}

	private function findParticipant($challonge_id, $participants) {
		return array_values( array_filter($participants, function ($p) use($challonge_id) {
			return $p->participant->id == $challonge_id;
		}));

	}
	/**
	 * Generar listado de users para challonge api
	 *
	 * @param $participantes
	 *
	 * @return array
	 */
	private function generar_usuarios( $participantes ) {
		$formated_users = [
			'name' => [],
			'invite_name_or_email' => []
		];
		$users = get_users([
			'include' => $participantes
		]);

		if( ! empty($users) && is_array($users) ) {
			$formated_users = array_map( function ($u) {
				return [
					'name' => $u->display_name,
					'invite_name_or_email' => $u->user_email,
					'misc'  => $u->ID
				];
			}, $users);
		}
		return $formated_users;
	}
}

new DartsL_Challonge();