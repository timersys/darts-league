<?php

use GuzzleHttp\Client;

class Challonge_Wrapper extends \Imbue\Challonge\Challonge {



	/**
	 * Buld Add a participant to a tournament.
	 *
	 * @param $tournament
	 * @param array $params
	 * @return mixed
	 */
	public function createParticipantBulk($tournament, array $params)
	{
		return $this->post("tournaments/{$tournament}/participants/bulk_add", [GuzzleHttp\RequestOptions::JSON   => $params]);
	}
	/**
	 * Start tournament.
	 *
	 * @param $tournament
	 * @param array $params
	 * @return mixed
	 */
	public function startTournament($tournament, array $params = [])
	{
		return $this->post("tournaments/{$tournament}/start", ['form_params' => $params]);
	}



	/**
	 * delete all tournament participants
	 * @param $tournament
	 *
	 * @return mixed
	 */
	public function clearParticipants($tournament) {
		return $this->delete("tournaments/{$tournament}/participants/clear");
	}


	/**
	 * Retrieve a single tournament.
	 *
	 * @param $tournament
	 *
	 * @param $params
	 *
	 * @return array
	 */
	public function getTorneo($tournament, $params)
	{

		return $this->makeRequest('get', "tournaments/{$tournament}", ['query' => array_merge($params, ['api_key' => $this->api_key]) ] );
	}

	/**
	 * @param $http_verb
	 * @param $method
	 * @param array $args
	 * @param int $timeout
	 * @return mixed
	 */
	protected function makeRequest($http_verb, $method, $args = [], $timeout = self::TIMEOUT)
	{
		$client = new Client([
			'timeout' => $timeout,
			'version' => $this->api_version,
			'base_uri' => $this->endpoint,
			'headers' => [
				'Accept' => $this->format,
				'Content-Type' => $this->format,
				'User-Agent' => "Challonge PHP wrapper {$this->api_version} (https://github.com/imbue/challonge-php) modified by Timersys"
			],
			'query' => [
				'api_key' => $this->api_key
			],
			'http_errors' => false,
		]);

		switch ($http_verb) {
			case 'get':
				$response = $client->get($method,$args);
				break;
			case 'post':
				$response = $client->post($method, $args);
				break;
			case 'put':
				$response = $client->put($method, $args);
				break;
			case 'delete':
				$response = $client->delete($method, $args);
				break;
		}

		$this->response = $response;

		return $this->parseResponse($response);
	}
}