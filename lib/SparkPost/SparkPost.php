<?php
namespace SparkPost;
use Ivory\HttpAdapter\Configuration;
use Ivory\HttpAdapter\HttpAdapterInterface;

class SparkPost {

  public $transmission;

  /**
   * Connection config for making requests.
   */
  private $config;

  /**
   * @var \Ivory\HttpAdapter\HttpAdapterInterface to make requests through.
   */
  public $httpAdapter;

  /**
   * Default config values. Passed in values will override these.
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
   * Sets up httpAdapter and config
   *
   * Sets up instances of sub libraries.
   *
   * @param \Ivory\HttpAdapter\HttpAdapterInterface $httpAdapter - An adapter for making http requests
   * @param String | array $settingsConfig - Hashmap that contains config values
   *              for the SDK to connect to SparkPost. If its a string we assume that
   *              its just they API Key.
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
   * @param string $endpoint
   * @return APIResource - the unwrapped resource
   */
  public function setupUnwrapped ($endpoint) {
    $this->{$endpoint} = new APIResource($this);
    $this->{$endpoint}->endpoint = $endpoint;

    return $this->{$endpoint};
  }

  /**
   * Merges passed in headers with default headers for http requests
   */
  public function getHttpHeaders() {
    $defaultOptions = [
        'Authorization' => $this->config['key'],
        'Content-Type' => 'application/json',
    ];

    return $defaultOptions;
  }

  /**
   * Helper function for getting the configuration for http requests
   * @param array $config
   * @return Configuration
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
   * Validates and sets up the httpAdapter
   * @param $httpAdapter \Ivory\HttpAdapter\HttpAdapterInterface to make requests through.
   * @throws \Exception
   */
  public function setHttpAdapter(HttpAdapterInterface $httpAdapter) {
    $this->httpAdapter = $httpAdapter;
    $this->httpAdapter->setConfiguration($this->getHttpConfig($this->config));
  }

  /**
   * Allows the user to pass in values to override the defaults and set their API key
   * @param String | array $settingsConfig - Hashmap that contains config values
   *              for the SDK to connect to SparkPost. If its a string we assume that
   *              its just they API Key.
   * @throws \Exception
   */
  public function setConfig($settingsConfig) {
    // if the config map is a string we should assume that its an api key
    if (is_string($settingsConfig)) {
      $settingsConfig = ['key'=>$settingsConfig];
    }

    // Validate API key because its required
    if (!isset($settingsConfig['key']) || !preg_match('/\S/', $settingsConfig['key'])){
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
