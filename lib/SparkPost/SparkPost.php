<?php
namespace SparkPost;
use Ivory\HttpAdapter\Configuration;
use Ivory\HttpAdapter\HttpAdapterInterface;

class SparkPost {

  public $transmission;

  /**
   * @dec connection config for making requests.
   */
  private $config;

  /**
   * @desc Ivory\HttpAdapter\HttpAdapterInterface to make requests through.
   */
  public $httpAdapter;

  /**
   * @desc Default config values. Passed in values will override these.
   */
  private static $apiDefaults = [
    'host'=>'api.sparkpost.com',
    'protocol'=>'https',
    'port'=>443,
    'strictSSL'=>true,
    'key'=>'',
    'version'=>'v1'
  ];

  /**
   * @desc sets up httpAdapter and config
   *
   * Sets up instances of sub libraries.
   *
   * @param Ivory\HttpAdapter $httpAdapter - An adapter for making http requests
   * @param Array $settingsConfig - Hashmap that contains config values for the SDK to connect to SparkPost
   */
  public function __construct($httpAdapter, $settingsConfig) {
    //config needs to be setup before adapter because of default adapter settings
    $this->setConfig($settingsConfig);
    $this->setHttpAdapter($httpAdapter);

    $this->transmission = new Transmission($this);
  }



  /**
   * Creates an unwrapped api interface for endpoints that aren't yet supported.
   * The new resource is attached to this object as well as returned
   * @return SparkPost\APIResource - the unwrapped resource
   */
  public function setupUnwrapped ($endpoint) {
    $this->{$endpoint} = new APIResource($this);
    $this->{$endpoint}->endpoint = $endpoint;

    return $this->{$endpoint};
  }

  /**
   * @desc Merges passed in headers with default headers for http requests
   */
  public function getHttpHeaders() {
    $defaultOptions = [
      'Authorization' => $this->config['key'],
      'Content-Type' => 'application/json',
    ];

    return $defaultOptions;
  }


  /**
   * @desc Helper function for getting the configuration for http requests
   * @return \Ivory\HttpAdapter\Configuration
   */
  private function getHttpConfig($config) {
    // get composer.json to extract version number
    $composerFile = file_get_contents(dirname(__FILE__) . '/../../composer.json');
    $composer = json_decode($composerFile, true);

    // create Configuration for http adapter
    $httpConfig = new Configuration();
    $baseUrl = $config['protocol'] . '://' . $config['host'] . ($config['port'] ? ':' . $config['port'] : '') . '/api/' . $config['version'];
    $httpConfig->setBaseUri($baseUrl);
    $httpConfig->setUserAgent('php-sparkpost/' . $composer['version']);
    return $httpConfig;
  }


  /**
   * @desc Validates and sets up the httpAdapter
   * @param $httpAdapter Ivory\HttpAdapter\HttpAdapterInterface to make requests through.
   * @throws \Exception
   */
  public function setHttpAdapter($httpAdapter) {
    if (!$httpAdapter instanceOf HttpAdapterInterface) {
      throw new \Exception('$httpAdapter paramter must be a valid Ivory\HttpAdapter');
    }

    $this->httpAdapter = $httpAdapter;
    $this->httpAdapter->setConfiguration($this->getHttpConfig($this->config));
  }


  /**
   * Allows the user to pass in values to override the defaults and set their API key
   * @param Array $settingsConfig - Hashmap that contains config values for the SDK to connect to SparkPost
   * @throws \Exception
   */
  public function setConfig(Array $settingsConfig) {
    // Validate API key because its required
    if (!isset($settingsConfig['key']) || empty(trim($settingsConfig['key']))){
      throw new \Exception('You must provide an API key');
    }

    $this->config = self::$apiDefaults;

    // set config, overriding defaults
    foreach ($settingsConfig as $configOption => $configValue) {
      if(key_exists($configOption, $this->config)) {
        $this->config[$configOption] = $configValue;
      }
    }
  }
}

?>
