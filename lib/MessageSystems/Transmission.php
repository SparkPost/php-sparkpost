<?php
namespace MessageSystems;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class Transmission {
	private $model;
	private $config;
	private $request;
	
	/**
	 * 
	 */
	public function __construct() {
		$this->config = Configuration::getConfig();
		$this->request = new Client();
		$this->model = [
			'campaign_id'=>null,
			'metadata'=>null,
			'substitution_data'=>null,
			'description'=>null,
			'return_path'=>null,
			'content' => [
				'reply_to'=>null,
				'subject'=>null,
				'from'=>null,
				'html'=>null,
				'text'=>null,
				'email_rfc822'=>null,
				'headers'=>null
			],
			'recipients'=>null
		];
	}
	
	
	private function getBaseUrl() {
		return $this->config['protocol'] . '://' . $this->config['host'] . ($this->config['port'] ? ':' . $this->config['port'] : '') .  $this->config['baseUrl'];
	}
	
	/**
	 * 
	 * @param unknown $config
	 * @param string $transmissionID
	 * @param string $callback
	 * @return multitype:multitype:string
	 */
	private function fetch ($transmissionID = null) {
		//figure out the url
		$url = $this->getBaseUrl();
		if (!is_null($transmissionID)){
			$url .= '/'.$transmissionID;
		}
		
		//make request
		try {
			$response = $this->request->get($url, [
				"headers"=>['authorization' => $this->config['key']]
				"verify"=>$this->config['strictSSL']
			]);
			
			if($response->getStatusCode() === 404) {
				throw new \Exception("The specified Transmission ID does not exist", 404);
			} else if ($response->getStatusCode() !== 200) {
				throw new \Exception("Received bad response from Transmission API: ". $response->getStatusCode());
			} else {
				$results = $response->json();
			}
			
		} catch (RequestException $exception) {
			throw new \Exception('Unable to contact Transmissions API: '. $exception->getMessage());	
		}
		return $results;
	}
	
	public function all() {
		return $this->fetch(); 
	}
	
	public function find($transmissionID) {
		$this->fetch($transmissionID);
	}
}

?>