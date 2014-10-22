<?php
namespace MessageSystems;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class Transmission {
	private $model;
	private $config;
	private $request;
	
	
	public function __construct($params = null) {
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
				'headers'=>null,
				'use_draft_template'=>false,
			],
			'options'=>[
				'open_tracking'=>true,
				'click_tracking'=>true
			],
			'recipients'=>null
		];
		
		if(!is_null($params)) {
			foreach($params as $key=>$value) {
				if(key_exists($key, $this->model)) {
					$this->model[$key] = $value;
				} else if (key_exists($key, $this->model['content'])) {
					$this->model['content'][$key] = $value;
				} else if (key_exists($key, $this->model['options'])) {
					$this->model['options'][$key] = $value;
				}
			}
		
			if (isset($params['recipientList'])) {
				$this->useRecipientList($params['recipientList']);
			}
		}
	}
	
	
	private function getBaseUrl() {
		return $this->config['protocol'] . '://' . $this->config['host'] . ($this->config['port'] ? ':' . $this->config['port'] : '') .  $this->config['baseUrl'];
	}
	
	
	public function send() {
		try {
			$response = $this->request->post($this->getBaseUrl(), [
				'json'=>$this->model,
				"headers"=>['authorization' => $this->config['key']],
				"verify"=>$this->config['strictSSL']
			]);
			
			$body = $response->json();
			
			if ($response->getStatusCode() !== 200) {
				return $body['errors'];
			}
			return $body;
			
		} catch (RequestException $exception) {
			throw new \Exception('Unable to contact Transmissions API: '. $exception->getMessage());
		}
	}
	
	/**
	 * 
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
				"headers"=>['authorization' => $this->config['key']],
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
	
	public function setMetadata ($meta) {
		$this->model['metadata'] = $meta;
		return $this;
	}
	
	public function setSubstitutiondata ($subs) {
		$this->model['substitution_data'] = $subs;
		return $this;
	}
	
	public function setCampaign ($campaignID) {
		$this->model['campaign_id'] = $campaignID;
		return $this;
	}
	
	public function setDescription ($description) {
		$this->model['description'] = $description;
		return $this;
	}
	
	public function setReturnPath ($returnPath) {
		$this->model['return_path'] = $returnPath;
		return $this;
	}
	
	public function setReplyTo ($replyTo) {
		$this->model['content']['reply_to'] = $replyTo;
		return $this;
	}
	
	public function setSubject ($subject) {
		$this->model['content']['subject'] = $subject;
		return $this;
	}
	
	public function setFrom ($fromField) {
		$this->model['content']['from'] = $fromField;
		return $this;
	}
	
	public function setHTMLContent ($html) {
		$this->model['content']['html'] = $html;
		return $this;
	}
	
	public function setTextContent ($plaintext) {
		$this->model['content']['text'] = $plaintext;
		return $this;
	}
	
	public function setRfc822Content ($rfc) {
		$this->model['content']['rfc'] = $rfc;
		return $this;
	}
	
	public function setContentHeaders ($headers) {
		$this->model['content']['headers'] = $headers;
		return $this;
	}
	
	public function addRecipient ($recipient) {
		if(!is_array($this->model['recipients'])) {
			$this->model['recipients'] = [];
		}
		$this->model['recipients'].push($recipient);
		return $this;
	}
	
	public function addRecipients ($recipients) {
		if(!is_array($this->model['recipients'])) {
			$this->model['recipients'] = [];
		}
		$this->model['recipients'] = array_merge($this->model['recipients'], $recipients);
		return $this;
	}
	
	public function useRecipientList ($recipientList) {
		//reset the recipients field
		$this->model['recipients'] = [];
		$this->model['recipients']['list_name'] = $recipientList;
		return $this;
	}
	
	public function useStoredTemplate ($templateID) {
		$this->model['content']['template_id'] = $templateID;
		return $this;
	}
	
	public function enableClickTracking () {
		$this->model['opitons']['click_tracking'] = true;
		return $this;
	}
	
	public function disableClickTracking () {
		$this->model['opitons']['click_tracking'] = false;
		return $this;
	}
	
	public function enableOpenTracking () {
		$this->model['opitons']['open_tracking'] = true;
		return $this;
	}
	
	public function disableOpenTracking () {
		$this->model['opitons']['open_tracking'] = false;
		return $this;
	}
	
	public function useDraftTemplate () {
		$this->model['opitons']['use_draft_template'] = true;
		return $this;
	}
	
	public function usePublishedTemplate () {
		$this->model['opitons']['use_draft_template'] = false;
		return $this;
	}
}

?>