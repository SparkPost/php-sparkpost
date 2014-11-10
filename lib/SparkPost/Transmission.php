<?php
namespace SparkPost;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

/**
 * @desc SDK interface for managing transmissions
 */
class Transmission {
	/**
	 * @desc singleton holder to create a guzzle http client 
	 * @var \GuzzleHttp\Client
	 */
	private static $request;
	
	/**
	 * @desc Mapping for values passed into the send method to the values needed for the Transmission API 
	 * @var array
	 */
	private static $parameterMappings = [
		'campaign'=>'campaign_id',
		'metadata'=>'metadata',
		'substitutionData'=>'substitution_data',
		'description'=>'description',
		'returnPath'=>'return_path',
		'replyTo'=>'content.reply_to',
		'subject'=>'content.subject',
		'from'=>'content.from',
		'html'=>'content.html',
		'text'=>'content.text',
		'rfc822Part'=>'content.email_rfc822',
		'customHeaders'=>'content.headers',
		'recipients'=>'recipients',
		'recipientList'=>'recipients.list_id',
		'template'=>'content.template_id',
		'trackOpens'=>'options.open_tracking',
		'trackClicks'=>'options.click_tracking',
		'useDraftTemplate'=>'use_draft_template'
	];
	
	/**
	 * @desc Sets up default structure and default values for the model that is acceptable by the API
	 * @var array
	 */
	private static $structure = [
		'return_path'=>"default@sparkpostmail.com",
		'content'=>[
			'html'=>null, 
			'text'=>null,
			'email_rfc822'=>null
		],
		'options'=>[
			'open_tracking'=>true, 
			'click_tracking'=>true
		],
		'use_draft_template'=>false
	];
	
	/**
	 * @desc Ensure that this class cannot be instansiated
	 */
	private function __construct() {}

	/**
	 * @desc Creates and returns a guzzle http client.
	 * @return \GuzzleHttp\Client
	 */
	private static function getHttpClient() {
		if(!isset(self::$request)) {
			self::$request = new Client();
		}
		return self::$request;
	}
	
	
	/**
	 * @desc Private Method helper to reference parameter mappings and set the right value for the right parameter
	 */
	private static function setMappedValue (&$model, $mapKey, $value) {
		//get mapping
		if(array_key_exists($mapKey, self::$parameterMappings)) {
			$temp = &$model;
			$path = explode('.', self::$parameterMappings[$mapKey]);
			foreach( $path as $key ) {
				$temp = &$temp[$key];
			}
			$temp = $value;
		} //ignore anything we don't have a mapping for
	}
	
	/**
	 * @desc Private Method helper to get the configuration values to create the base url for the transmissions API
	 * 
	 * @return string base url for the transmissions API
	 */
	private static function getBaseUrl($config) {
		$baseUrl = '/api/' . $config['version'] . '/transmissions';
		return $config['protocol'] . '://' . $config['host'] . ($config['port'] ? ':' . $config['port'] : '') .  $baseUrl;
	}
	
	/**
	 * @desc Method for issuing POST request to the Transmissions API
	 *
	 *  This method assumes that all the appropriate fields have
	 *  been populated by the user through configuration.  Acceptable 
	 *  configuration values are: 
	 *  'campaign': string, 
	 *	'metadata': array,
	 *	'substitutionData': array,
	 *	'description': string,
	 *	'replyTo': string,
	 *	'subject': string,
	 *	'from': string,
	 *	'html': string,
	 *	'text': string,
	 *	'rfc822Part': string,
	 *	'customHeaders': array,
	 *	'recipients': array,
	 *	'recipientList': string,
	 *	'template': string,
	 *	'trackOpens': boolean,
	 *	'trackClicks': boolean,
	 *	'useDraftTemplate': boolean 
	 *
	 * @return array API repsonse represented as key-value pairs
	 */
	public static function send($transmissionConfig) {
		$hostConfig = SparkPost::getConfig();
		$request = self::getHttpClient();
		
		//create model from $transmissionConfig
		$model = self::$structure;
		foreach($transmissionConfig as $key=>$value) {
			self::setMappedValue($model, $key, $value);
		}
		
		//send the request
		try {
			$response = $request->post(self::getBaseUrl($hostConfig), [
				'json'=>$model,
				"headers"=>['authorization' => $hostConfig['key']],
				"verify"=>$hostConfig['strictSSL']
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
	private static function fetch ($transmissionID = null) {
		//figure out the url
		$hostConfig = SparkPost::getConfig();
		$url = self::getBaseUrl($hostConfig);
		if (!is_null($transmissionID)){
			$url .= '/'.$transmissionID;
		}
		
		$request = self::getHttpClient();
		
		//make request
		try {	
			$response = $request->get($url, [
				"headers"=>['authorization' => $hostConfig['key']],
				"verify"=>$hostConfig['strictSSL']
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
	public static function all() {
		return self::fetch(); 
	}
	
	/**
	 * @desc Method for retrieving information about a single transmission
 	 *  Wrapper method for a cleaner interface
 	 *  
	 * @param string $transmissionID Identifier of the transmission to be found
	 * @return array result Single transmission represented in key-value pairs
	 */
	public static function find($transmissionID) {
		return self::fetch($transmissionID);
	}
}

?>