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
		require_once DARTSL_PLUGIN_DIR . 'vendor/autoload.php';
		$this->challonge = new Challonge('dYMrIaPtMKsIBAMpU7v8M5MOEUqoFtLEMd6uppfH');
		add_action( 'wp_ajax_generar_llave', [ $this, 'generar_llave'] );
	}

	public function generar_llave() {
		if( empty($_POST['participantes']) ){
			return json_encode(['error' => 'Al menos tiene que haber 3 participantes']);
			die();
		}


		$tourament = $this->challonge->createTournament(
			[
			'tournament' => [
				'name' => 'Tournament name',
				'url' => 'imbues_new_tournament',
		    ]
		]);

	}
}

new DartsL_Challonge();