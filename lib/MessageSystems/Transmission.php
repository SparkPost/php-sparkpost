<?php
namespace MessageSystems;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

/**
 * @desc SDK interface for managing transmissions
 */
class Transmission {
	private $model;
	private $config;
	private $request;
	
	private $parameterMappings = [
		'campaign'=>'setCampaign',
		'metadata'=>'setMetadata',
		'substitutionData'=>'setSubstitutionData',
		'description'=>'setDescription',
		'returnPath'=>'setReturnPath',
		'replyTo'=>'setReplyTo',
		'subject'=>'setSubject',
		'from'=>'setFrom',
		'html'=>'setHTMLContent',
		'text'=>'setTextContent',
		'rfc822Part'=>'setRfc822Content',
		'headers'=>'setContentHeaders',
		'recipients'=>'addRecipients',
		'recipientList'=>'useRecipientList',
		'template'=>'useStoredTemplate',
		'openTracking'=>1,
		'clickTracking'=>1,
		'useDraftTemplate'=>1
	];
	
	/**
	 * @desc Sets up an object for managaging Transmissions
	 * 	 
	 * 	Please note that key values that can be specified are:
	 * 		- campaign
	 * 		- metadata
	 * 		- substitutionData
	 * 		- description
	 * 		- returnPath
	 * 		- replyTo
	 * 		- subject
	 * 		- from
	 * 		- html
	 * 		- text
	 * 		- rfc822Part
	 * 		- headers
	 * 		- recipients
	 * 		- recipientList
	 * 		- template
	 * 		- openTracking
	 * 		- clickTracking
	 * 		- useDraftTemplate
	 * 
	 * @param array $params (optional) key-value pairs representing the Transmission's settings
	 */
	public function __construct($params = null) {
		$this->config = Configuration::getConfig();
		
		$this->config['baseUrl'] = '/api/' . $this->config['version'] . '/transmissions';
		
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
				'template_id'=>null,
				'use_draft_template'=>false,
			],
			'options'=>[
				'open_tracking'=>true,
				'click_tracking'=>true
			],
			'recipients'=>null
		];
		
		//if params were supplied, set their values on the model
		if(!is_null($params)) {
			foreach($params as $key=>$value) {
				$this->setMappedValue($key, $value);
			}
		}
	}
	
	/**
	 * @desc Private Method helper to reference parameter mappings and set the right value for the right parameter
	 */
	private function setMappedValue ($mapKey, $value) {
		//get mapping
		if(array_key_exists($mapKey, $this->parameterMappings)) {
			// if its an option, set the value directly
			if(in_array($mapKey, ['openTracking', 'clickTracking', 'useDraftTemplate'])) {
				switch ($mapKey) {
					case 'openTracking':
						$this->model['options']['open_tracking'] = $value;
						break;
					case 'clickTracking':
						$this->model['options']['click_tracking'] = $value;
						break;
					case 'useDraftTemplate':
						$this->model['content']['use_draft_template'] = $value;
						break;
				}
			} else { //otherwise call the method to set the value
				$method = $this->parameterMappings[$mapKey];
				$this->$method($value);
			}
		} //ignore anything we don't have a mapping for
	}
	
	/**
	 * @desc Private Method helper to get the configuration values to create the base url for the transmissions API
	 * 
	 * @return string base url for the transmissions API
	 */
	private function getBaseUrl() {
		return $this->config['protocol'] . '://' . $this->config['host'] . ($this->config['port'] ? ':' . $this->config['port'] : '') .  $this->config['baseUrl'];
	}
	
	/**
	 * @desc Method for issuing POST request to the Transmissions API
	 *
	 *  This method assumes that all the appropriate fields have
	 *  been populated by the user through configuration or calling
	 *  helper methods
	 *
	 * @return array API repsonse represented as key-value pairs
	 */
	public function send() {
		try {
			$response = $this->request->post($this->getBaseUrl(), [
				'json'=>$this->model,
				"headers"=>['authorization' => $this->config['key']],
				"verify"=>$this->config['strictSSL']
			]);
			return $response->json();
		} catch (RequestException $exception) {
			$response = $exception->getResponse();
			throw new \Exception(json_encode($response->json()['errors']));
		} catch (\Exception $exception) {
			throw new \Exception('Unable to contact Transmissions API: '. $exception->getMessage());
		}
	}
	
	/**
	 * @desc Private Method for issuing GET request to Transmissions API
	 *
	 *  This method is responsible for getting the collection _and_
	 *  a specific entity from the Transmissions API
	 *
	 *  If TransmissionID parameter is omitted, then we fetch the collection
	 *
	 * @param string $transmissionID (optional) string Transmission ID of specific Transmission to retrieve
	 * @return array Result set of transmissions found
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
			return $response->json();
		} catch (RequestException $exception) {
			$response = $exception->getResponse();
			if($response->getStatusCode() === '404') {
				throw new \Exception("The specified Transmission ID does not exist", 404);
			} else {
				throw new \Exception("Received bad response from Transmission API: ". $response->getStatusCode());
			}
		} catch (\Exception $exception) {
			throw new \Exception('Unable to contact Transmissions API: '. $exception->getMessage());
		}
	}
	
	/**
	 * @desc Method for retrieving information about all transmissions
	 *  Wrapper method for a cleaner interface
	 *  
	 * @return array result Set of transmissions
	 */
	public function all() {
		return $this->fetch(); 
	}
	
	/**
	 * @desc Method for retrieving information about a single transmission
 	 *  Wrapper method for a cleaner interface
 	 *  
	 * @param string $transmissionID Identifier of the transmission to be found
	 * @return array result Single transmission represented in key-value pairs
	 */
	public function find($transmissionID) {
		return $this->fetch($transmissionID);
	}
	
	/**
	 * @desc Method for adding metadata to a Transmission
	 *
	 *  Metadata for a Transmission are key-value pairs that will be made available
	 *  in the webhooks payloads as identifiers for events that are associated with a particular Transmission
	 *
	 *  Please note that metadata can be applied at the recipient level, and any recipient level metadata takes
	 *  precedence over Transmission level metadata
	 * 
	 * @param array $meta Key-value pairs to be applied to the Transmission level metadata
	 * @return \MessageSystems\Transmission Object
	 */
	public function setMetadata ($meta) {
		$this->model['metadata'] = $meta;
		return $this;
	}
	
	/**
	 * @desc Method for adding substitution data to a Transmission
	 *
	 *  Substitution data are key-value pairs that are provided
	 *  to the subsititution engine. The substitution engine scans
	 *  parts of the content for substitution syntax '{{ }}' and
	 *  substitutes the values of a given key where the syntax was found
	 *
	 *  Please note that recipient level substitution data takes precedence
	 *  over any Transmission level substitution data
	 *
	 * @param array $subs Key-value pairs of substitution data to be applied at the Transmission level
	 * @return \MessageSystems\Transmission Object
	 */
	public function setSubstitutiondata ($subs) {
		$this->model['substitution_data'] = $subs;
		return $this;
	}
	
	/**
	 * @desc Method for adding a campaign to a Transmission
	 *
	 *  Campaigns are logical groupings of related Transmissions
	 *
	 *  For example, I may have multiple mailings related to my Labor Day Sale,
	 *  and would apply the campaign 'LaborDay2k14' to all Transmissions associated with
	 *  said sale.
	 *
	 *  It is also worth noting that Transmissions flagged with a given campaign will be available
	 *  for filtering in both webhooks, as well as the Reporting UI/Metrics API
	 *
	 * @param string $campaignID Campaign Name, with a max length of 64 bytes
	 * @return \MessageSystems\Transmission Object
	 */
	public function setCampaign ($campaignID) {
		$this->model['campaign_id'] = $campaignID;
		return $this;
	}
	
	/**
	 * @desc Method for adding a description to a Transmission
	 *
	 *  Descriptions are arbitrary strings used further describe what a specific
	 *  Transmission was/is for the user's benefit.
	 *
	 *  Please note that the only place currently that description is exposed is via
	 *  the Transmissions API, and the fetch method of this SDK
	 *
	 * @param string $description Description of a Transmission with a max length of 1024 bytes
	 * @return \MessageSystems\Transmission Object
	 */
	public function setDescription ($description) {
		$this->model['description'] = $description;
		return $this;
	}

	/**
	 * @desc Method for adding a Return Path to a Transmission
	 *
	 *  A return path is an email address supplied to a Transmission where any
	 *  bounces generated 'in the wild' will be sent back to this address. Return
	 *  Path is used at the server level.
	 *
	 *  Please note that this field can be specified in recipients if using
	 *  VERP (Variable Envelope Return Path), which will give each recipient
	 *  a unique envelope MAIL FROM
	 *
	 * @param string $returnPath Return Path to be applied to a Transmission
	 * @return \MessageSystems\Transmission Object
	 */
	public function setReturnPath ($returnPath) {
		$this->model['return_path'] = $returnPath;
		return $this;
	}
	
	/**
	 * @desc Method for adding a Reply To to a Transmission
	 *
	 *  A Reply To is very similar to Return Path, but instead of
	 *  being used by servers, Reply To is used by humans. When a human
	 *  in their mail client clicks the reply button, the To field of that email
	 *  will be populated with the email address provided in the Reply To field
	 *
	 * @param string $replyTo Reply To to be applied to a Transmission
	 * @return \MessageSystems\Transmission Object
	 */
	public function setReplyTo ($replyTo) {
		$this->model['content']['reply_to'] = $replyTo;
		return $this;
	}
	
	/**
	 * @desc Method for adding a Subject to a Transmission
	 *
	 *  Sets the subject line of content for a given Transmission
	 *
	 * @param string $subject Subject to be applied to a Transmission
	 * @return \MessageSystems\Transmission Object
	 */
	public function setSubject ($subject) {
		$this->model['content']['subject'] = $subject;
		return $this;
	}
	
	/**
	 * @desc Method for adding a From to a Transmission
	 *
	 *  Sets the from header of content for a given Transmission.
	 *  Please note that there are three ways to provide the from header
	 *
	 *  1. From is a string, like 'person@example.com'
	 *  2. From is an object with a key email, like '{email: 'person@example.com'}'
	 *  3. From is an object with a email and name key, like '{name: 'Jane Doe', email: 'person@example.com'}'
	 *
	 *  Using the third form of From will result is a 'pretty' From headers, like From: Jane Doe <person@example.com>
	 *
	 * @param mixed $fromField array/string From header to be applied to a Transmission
	 * @return \MessageSystems\Transmission Object
	 */
	public function setFrom ($fromField) {
		$this->model['content']['from'] = $fromField;
		return $this;
	}
	
	/**
	 * @desc Method for adding HTML content to a Transmission
	 *
	 *  Used for generating a Transmission using inline HTML content
	 *  Please note that you cannot specify HTML content if you've also
	 *  provided a stored template (useStoredTemplate)
	 *
	 *  You cannot specify HTML content if you've also provided RFC-822
	 *  encoded content
	 *
	 * @param string $html HTML Content to be used when the Transmission is sent
	 * @return \MessageSystems\Transmission Object
	 */
	public function setHTMLContent ($html) {
		$this->model['content']['html'] = $html;
		return $this;
	}
	
	/**
	 * @desc Method for adding Plain Text content to a Transmission
	 *
	 *  Use for generating a Transmission using line Plain Text content
	 *  Please note that you cannot specify Plain Text content if you've also
	 *  provided a stored template (useStoredTemplate)
	 *
	 *  You cannot specify Plain Text content if you've also provided RFC-822
	 *  encoded content
	 *
	 * @param string $plaintext Plain Text Content to be used when the Transmission is sent
	 * @return \MessageSystems\Transmission Object
	 */
	public function setTextContent ($plaintext) {
		$this->model['content']['text'] = $plaintext;
		return $this;
	}
	
	/**
	 * @desc Method for adding RFC 822 encoded content to a Transmission
	 *
	 *  Used for generating a Transmission using inline encoded content
	 *  Please note that you cannot specify RFC-822 content if you've also
	 *  provided a stored template (useStoredTemplate)
	 *
	 *  You cannot specify RFC-822 content if you've already provided HTML
	 *  or Plain Text content
	 *
	 * @param string $rfc RFC-822 encoded content to be used when the Transmission is sent
	 * @return \MessageSystems\Transmission Object
	 */
	public function setRfc822Content ($rfc) {
		$this->model['content']['email_rfc822'] = $rfc;
		return $this;
	}
	
	/**
	 * @desc Method for adding custom headers to the content of a Transmission
	 *
	 *  Can contain any key-value pairs _except_
	 *  - Subject
	 *  - From
	 *  - To
	 *  - Reply-To
	 *
	 * @param array $headers Key-value pairs of headers to add to the content of a Transmission
	 * @return \MessageSystems\Transmission Object
	 */
	public function setContentHeaders (Array $headers) {
		$this->model['content']['headers'] = $headers;
		return $this;
	}
	
	/**
	 * @desc Method for adding a single recipient to a Transmission
	 *
	 *  Used for supplying inline recipients for a Transmission. Emails will be generated
	 *  for each recipient in the list.
	 *
	 *  The only required field in the recipient definition is address, all others are optional
	 *  If using multiple recipients, iteratively call this method, or use addRecipients
	 *
	 *  If you call useRecipientList after using this method, recipient value will be overridden
	 *  
	 * @param array $recipient An associative array of recipient data to send a Transmission
	 * @return \MessageSystems\Transmission Object
	 */
	public function addRecipient (Array $recipient) {
		if(!is_array($this->model['recipients'])) {
			$this->model['recipients'] = [];
		}
		array_push($this->model['recipients'], $recipient);
		return $this;
	}
	
	/**
	 * @desc Method for adding multiple recipients at once to a Transmission
	 *
	 *  Used for supplying inline recipients for a Transmission. Emails will be generated
	 *  for each recipient in the list.
	 *
	 *  The only required field in the recipient definition is address, all others are optional
	 *
	 *  If you call useRecipientList after using this method, recipients value will be overridden
	 *  
	 * @param array $recipients An array of associative arrays containing recipient data
	 * @return \MessageSystems\Transmission Object
	 */
	public function addRecipients (Array $recipients) {
		if(!is_array($this->model['recipients'])) {
			$this->model['recipients'] = [];
		}
		$this->model['recipients'] = array_merge($this->model['recipients'], $recipients);
		return $this;
	}
	
	/**
	 * @desc Method for specifying a stored recipient list
	 *
	 *  Used for supplying a Transmission with recipients from a stored recipient list
	 *  Please note that you cannot use a stored recipient list _and_ inline recipients (addRecipient)
	 *  If you use addRecipient or addRecipients after using this method, recipientList value
	 *  will be overridden
	 *  
	 * @param string $recipientList Name of the recipient list to be used during Transmission
	 * @return \MessageSystems\Transmission Object
	 */
	public function useRecipientList ($recipientList) {
		// Resetting the data type as it could've been an array of inline recipients
		$this->model['recipients'] = [];
		$this->model['recipients']['list_id'] = $recipientList;
		return $this;
	}
	
	/**
	 * @desc Method for specifying a stored template
	 *
	 *  Used for supplying a Transmission with content from a stored template
	 *  Please note that you cannot use a stored template if you've also added inline
	 *  HTML, Plain Text, or RFC-822 encoded content
	 *  
	 * @param string $templateID Name of template to be used during Transmission
	 * @return \MessageSystems\Transmission Object
	 */
	public function useStoredTemplate ($templateID) {
		$this->model['content']['template_id'] = $templateID;
		return $this;
	}
	
	/**
	 * @desc Method for enabling click tracking for a given transmission
	 * 	By default, click tracking is enabled for a transmission
	 * 
	 * @return \MessageSystems\Transmission Object
	 */
	public function enableClickTracking () {
		$this->model['options']['click_tracking'] = true;
		return $this;
	}
	
	/**
	 * @desc Method for disabling click tracking for a given transmission
	 * 	By default, click tracking is enabled for a transmission
	 * 
	 * @return \MessageSystems\Transmission Object
	 */
	public function disableClickTracking () {
		$this->model['options']['click_tracking'] = false;
		return $this;
	}
	
	/**
	 * @desc Method for enabling open tracking for a given transmission
	 * 	By default, open tracking is enabled for a transmission
	 * 
	 * @return \MessageSystems\Transmission Object
	 */
	public function enableOpenTracking () {
		$this->model['options']['open_tracking'] = true;
		return $this;
	}
	
	/**
	 * @desc Method for disabling open tracking for a given transmission
	 * 	By default, open tracking is enabled for a transmission
	 * 
	 * @return \MessageSystems\Transmission Object
	 */
	public function disableOpenTracking () {
		$this->model['options']['open_tracking'] = false;
		return $this;
	}
	
	/**
	 * @desc Method for allowing the sending of a draft version of a template with a transmission
	 * 	By default, you cannot send a draft version of a stored template
	 * 
	 * @return \MessageSystems\Transmission Object
	 */
	public function useDraftTemplate () {
		$this->model['content']['use_draft_template'] = true;
		return $this;
	}
	
	/**
	 * @desc Method for disallowing the sending of a draft version of a template with a transmission
	 * 	By default, you cannot send a draft version of a stored template
	 * 
	 * @return \MessageSystems\Transmission Object
	 */
	public function usePublishedTemplate () {
		$this->model['content']['use_draft_template'] = false;
		return $this;
	}
}

?>