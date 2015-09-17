<?php
namespace SparkPost;
use Ivory\HttpAdapter\HttpAdapterException;

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

  /**
   * TODO: Docs
   */
	protected static function buildRequestModel(Array $requestConfig, Array $model=[] ) {
		foreach($requestConfig as $key=>$value) {
			self::setMappedValue($model, $key, $value);
		}
		return $model;
	}

	/**
	 * TODO: Docs
	 */
	public static function create(Array $body=[]) {
		return self::callResource( 'post', null, ['body'=>$body]);
	}

  /**
	 * TODO: Docs
	 */
	public static function update( $resourcePath, Array $body=[]) {
		return self::callResource( 'put', $resourcePath, ['body'=>$body]);
	}

	/**
	 * @desc Wrapper method for issuing GET request to current API endpoint
	 *
	 * @param string $resourcePath (optional) string resource path of specific resource
	 * @param array $options (optional) query string parameters
	 * @return array Result set of transmissions found
	 */
	public static function get( $resourcePath=null, Array $query=[] ) {
		return self::callResource( 'get', $resourcePath, ['query'=>$query] );
	}

	/**
	 * @desc Wrapper method for issuing DELETE request to current API endpoint
	 *
	 * @param string $resourcePath (optional) string resource path of specific resource
	 * @param array $options (optional) query string parameters
	 * @return array Result set of transmissions found
	 */
	public static function delete( $resourcePath=null, Array $query=[] ) {
		return self::callResource( 'delete', $resourcePath, ['query'=>$query] );
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
	private static function callResource( $action, $resourcePath=null, $options=[] ) {
		$action = strtoupper($action); // normalize

		if( !in_array($action, ['POST', 'PUT', 'GET', 'DELETE'])) {
			throw new \Exception('Invalid resource action');
		}

		$url = '/' . static::$endpoint . '/';
		$body = null;
		if (!is_null($resourcePath)){
			$url .= $resourcePath;
		}

		// untested:
		if( !empty($options['query'])) {
			$queryString = http_build_query($options['query']);
			$url .= '?'.$queryString;
		}

		if( !empty($options['body']) ) {
			$model = static::$structure;
			$requestModel = self::buildRequestModel( $options['body'], $model );
			$body = json_encode($requestModel);
		}

		//make request
		try {
			$httpAdapter = SparkPost::getHttpAdapter();
			$response = SparkPost::getHttpAdapter()->send($url, $action, SparkPost::getHttpHeaders(), $body);
			return json_decode($response->getBody()->getContents(), true);
		}
		/*
		 * Handles 4XX responses
     */
    catch (HttpAdapterException $exception) {
    	$response = $exception->getBody();
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
