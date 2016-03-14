<?php
namespace SparkPost;

/**
 * SDK interface for managing SparkPost API endpoints
 */
class APIResource {

  /**
   * name of the API endpoint, mainly used for URL construction.
   * This is public to provide an interface
   *
   * @var string
   */
  public $endpoint;

  /**
   * Mapping for values passed into the send method to the values needed for the respective API
   * @var array
   */
  protected static $parameterMappings = [];

  /**
   * Sets up default structure and default values for the model that is acceptable by the API
   * @var array
   */
  protected static $structure = [];

  /**
   * SparkPost reference for httpAdapters and configs
   */
  protected $sparkpost;

  /**
   * Initializes config and httpAdapter for use later.
   * @param $sparkpost \SparkPost\SparkPost provides api configuration information
   */
  public function __construct(SparkPost $sparkpost) {
    $this->sparkpost = $sparkpost;
  }

  /**
   * Private Method helper to reference parameter mappings and set the right value for the right parameter
   *
   * @param array $model (pass by reference) the set of values to map
   * @param string $mapKey a dot syntax path determining which value to set
   * @param mixed $value value for the given path
   */
  protected function setMappedValue(&$model, $mapKey, $value) {
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
   * maps values from the passed in model to those needed for the request
   * @param array $requestConfig the passed in model
   * @param array $model the set of defaults
   * @return array A model ready for the body of a request
   */
  protected function buildRequestModel(Array $requestConfig, Array $model=[] ) {
    foreach($requestConfig as $key => $value) {
      $this->setMappedValue($model, $key, $value);
    }
    return $model;
  }

  /**
   * posts to the api with a supplied body
   * @param array $body post body for the request
   * @return array Result of the request
   */
  public function create(Array $body=[]) {
    return $this->callResource( 'post', null, ['body'=>$body]);
  }

  /**
   * Makes a put request to the api with a supplied body
   * @param $resourcePath
   * @param array $body Put body for the request
   * @return array Result of the request
   * @throws APIResponseException
   */
  public function update( $resourcePath, Array $body=[]) {
    return $this->callResource( 'put', $resourcePath, ['body'=>$body]);
  }

  /**
   * Wrapper method for issuing GET request to current API endpoint
   *
   * @param string $resourcePath (optional) string resource path of specific resource
   * @param array $query (optional) query string parameters
   * @return array Result of the request
   */
  public function get( $resourcePath=null, Array $query=[] ) {
    return $this->callResource( 'get', $resourcePath, ['query'=>$query] );
  }

  /**
   * Wrapper method for issuing DELETE request to current API endpoint
   *
   * @param string $resourcePath (optional) string resource path of specific resource
   * @param array $query (optional) query string parameters
   * @return array Result of the request
   */
  public function delete( $resourcePath=null, Array $query=[] ) {
    return $this->callResource( 'delete', $resourcePath, ['query'=>$query] );
  }


  /**
   * assembles a URL for a request
   * @param string $resourcePath path after the initial endpoint
   * @param array $options array with an optional value of query with values to build a querystring from.
   * @return string the assembled URL
   */
  private function buildUrl($resourcePath, $options) {
    $url = "/{$this->endpoint}/";
    if (!is_null($resourcePath)){
      $url .= $resourcePath;
    }

    if( !empty($options['query'])) {
      $queryString = http_build_query($options['query']);
      $url .= '?'.$queryString;
    }

    return $url;
  }


  /**
   * Prepares a body for put and post requests
   * @param array $options array with an optional value of body with values to build a request body from.
   * @return string|null A json encoded string or null if no body was provided
   */
  private function buildBody($options) {
    $body = null;
    if( !empty($options['body']) ) {
      $model = static::$structure;
      $requestModel = $this->buildRequestModel( $options['body'], $model );
      $body = json_encode($requestModel);
    }
    return $body;
  }

  /**
   * Private Method for issuing GET and DELETE request to current API endpoint
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
   * @throws APIResponseException
   */
  private function callResource( $action, $resourcePath=null, $options=[] ) {
    $action = strtoupper($action); // normalize

    $url = $this->buildUrl($resourcePath, $options);
    $body = $this->buildBody($options);

    //make request
    try {
      $response = $this->sparkpost->httpAdapter->send($url, $action, $this->sparkpost->getHttpHeaders(), $body);

      $statusCode = $response->getStatusCode();

      // Handle 4XX responses, 5XX responses will throw an HttpAdapterException
      if ($statusCode < 400) {
        return json_decode($response->getBody()->getContents(), true);
      }
      elseif ($statusCode === 404) {
        throw new APIResponseException('The specified resource does not exist', 404);
      }
      else {
        $response = json_decode($response->getBody(), true);
        throw new APIResponseException(
          'Received bad response from ' . ucfirst($this->endpoint),
          $statusCode,
          isset($response['errors'][0]['message']) ? $response['errors'][0]['message'] : "",
          isset($response['errors'][0]['code']) ? $response['errors'][0]['code'] : 0,
          isset($response['errors'][0]['description']) ? $response['errors'][0]['description'] : ""
        );
      }
    }

    /*
     * Configuration Errors, and a catch all for other errors
     */
    catch (\Exception $exception) {
      if($exception instanceof APIResponseException) {
        throw $exception;
      }

      throw new APIResponseException('Unable to contact ' . ucfirst($this->endpoint) . ' API: '. $exception->getMessage(), $exception->getCode());
    }
  }

}
