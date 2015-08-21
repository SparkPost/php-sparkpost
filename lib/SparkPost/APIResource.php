<?php
namespace SparkPost;
use Guzzle\Http\Client;
use Guzzle\Http\Exception\ClientErrorResponseException;

/**
 * @desc SDK interface for managing SparkPost API endpoints
 */
class APIResource {
	
	/**
	 * @desc name of the API endpoint, mainly used for URL construction.
	 * @var string
	 */
	public static $endpoint;
	
	/**
	 * @desc singleton holder to create a guzzle http client 
	 * @var \GuzzleHttp\Client
	 */
	protected static $request;
	
	/**
	 * @desc Mapping for values passed into the send method to the values needed for the respective API
	 * @var array
	 */
	protected static $parameterMappings = array();
	
	/**
	 * @desc Sets up default structure and default values for the model that is acceptable by the API
	 * @var array
	 */
	protected static $structure = array();
	
	/**
	 * @desc Ensure that this class cannot be instansiated
	 */
	private function __construct() {}

	/**
	 * @desc Creates and returns a guzzle http client.
	 * @return \GuzzleHttp\Client
	 */
	protected static function getHttpClient() {
		if(!isset(self::$request)) {
			self::$request = new Client();
		}
		return self::$request;
	}
	
	/**
	 * @desc Private Method helper to get the configuration values to create the base url for the current API endpoint
	 * 
	 * @return string base url for the transmissions API
	 */
	protected static function getBaseUrl($config) {
		$baseUrl = '/api/' . $config['version'] . '/' . static::$endpoint;
		return $config['protocol'] . '://' . $config['host'] . ($config['port'] ? ':' . $config['port'] : '') .  $baseUrl;
	}
	
	
	/**
	 * @desc Private Method helper to reference parameter mappings and set the right value for the right parameter
	 */
	protected static function setMappedValue (&$model, $mapKey, $value) {
		//get mapping
		if( empty(static::$parameterMappings) ) {
			// if parameterMappings is empty we can assume that no wrapper is defined
			// for the current endpoint and we will use the mapKey to define the mappings directly
			$mapPath = $mapKey;
		}elseif(array_key_exists($mapKey, static::$parameterMappings)) {
			// use only defined parameter mappings to construct $model
			$mapPath = static::$parameterMappings[$mapKey];
		} else {
			return;
		}
		
		$path = explode('.', $mapPath);
		$temp = &$model;
		foreach( $path as $key ) {
			if( !isset($temp[$key]) ){
				$temp[$key] = null;
			}
			$temp = &$temp[$key];
		}
		$temp = $value;
		
	}
	
	protected static function buildRequestModel( $requestConfig, $model=array() ) {
		foreach($requestConfig as $key=>$value) {
			self::setMappedValue($model, $key, $value);
		}
		return $model;
	}
	
	/**
	 * @desc Method for issuing POST requests
	 *
	 * @return array API repsonse represented as key-value pairs
	 */
	public static function sendRequest( $requestConfig ) {
		$hostConfig = SparkPost::getConfig();
		$request = self::getHttpClient();
		
		//create model from $transmissionConfig
		$model = static::$structure;
		$requestModel = self::buildRequestModel( $requestConfig, $model );
		
		//send the request
		try {
			$response = $request->post(
				self::getBaseUrl($hostConfig),
				array('authorization' => $hostConfig['key']),
				json_encode($requestModel),
				array("verify"=>$hostConfig['strictSSL'])
			)->send();
			
			return $response->json();
		} 
		/*
		 * Handles 4XX responses
		 */
		catch (ClientErrorResponseException $exception) {
			$response = $exception->getResponse();
			$responseArray = $response->json();
			throw new \Exception(json_encode($responseArray['errors']));	
		} 
		/*
		 * Handles 5XX Errors, Configuration Errors, and a catch all for other errors
		 */
		catch (\Exception $exception) { 
			throw new \Exception("Unable to contact ".ucfirst(static::$endpoint)." API: ". $exception->getMessage());
		}
	}

	
	/**
	 * @desc Wrapper method for issuing GET request to current API endpoint
	 *
	 * @param string $resourcePath (optional) string resource path of specific resource
	 * @param array $options (optional) query string parameters
	 * @return array Result set of transmissions found
	 */
	public static function fetchResource( $resourcePath=null, $options=array() ) {
		return self::callResource( 'get', $resourcePath, $options );
	}
	
	/**
	 * @desc Wrapper method for issuing DELETE request to current API endpoint
	 *
	 * @param string $resourcePath (optional) string resource path of specific resource
	 * @param array $options (optional) query string parameters
	 * @return array Result set of transmissions found
	 */
	public static function deleteResource( $resourcePath=null, $options=array() ) {
		return self::callResource( 'delete', $resourcePath, $options );
	}
	
	/**
	 * @desc Private Method for issuing GET and DELETE request to current API endpoint
	 *
	 *  This method is responsible for getting the collection _and_
	 *  a specific entity from the API endpoint
	 *
	 *  If resourcePath parameter is omitted, then we fetch the collection
	 *
	 * @param string $action HTTP method type
	 * @param string $resourcePath (optional) string resource path of specific resource
	 * @param array $options (optional) query string parameters
	 * @return array Result set of action performed on resource
	 */
	private static function callResource( $action, $resourcePath=null, $options=array() ) {
		
		if( !in_array( $action, array('get', 'delete') ) ) throw new \Exception('Invalid resource action');
		
		//build the url
		$hostConfig = SparkPost::getConfig();
		$url = self::getBaseUrl($hostConfig);
		if (!is_null($resourcePath)){
			$url .= '/'.$resourcePath;
		}
		
		// untested:
		if( !empty($options) ) {
			$queryString = http_build_query($options);
			$url .= '?'.$queryString;
		}
		
		$request = self::getHttpClient();
		
		//make request
		try {
			$response = $request->{$action}($url, array('authorization' => $hostConfig['key']), array("verify"=>$hostConfig['strictSSL']))->send();
			return $response->json();
		}
		/*
		 * Handles 4XX responses
		 */
		catch (ClientErrorResponseException $exception) {
			$response = $exception->getResponse();
			$statusCode = $response->getStatusCode();
			if($statusCode === 404) {
				throw new \Exception("The specified resource does not exist", 404);
			}
			throw new \Exception("Received bad response from ".ucfirst(static::$endpoint)." API: ". $statusCode );
		}
		/*
		 * Handles 5XX Errors, Configuration Errors, and a catch all for other errors
		 */
		catch (\Exception $exception) {
			throw new \Exception("Unable to contact ".ucfirst(static::$endpoint)." API: ". $exception->getMessage());
		}
	}
	
}
