<?php
namespace SparkPost;
class SparkPost {

  public $transmission;

  /**
   * @desc sets up httpAdapter and config
   *
   * Sets up instances of sub libraries.
   *
   * @param Ivory\HttpAdapter $httpAdapter - An adapter for making http requests
   * @param Array $settingsConfig - Hashmap that contains config values for the SDK to connect to SparkPost
   */
	public function __construct($httpAdapter, $settingsConfig) {
    $this->transmission = new Transmission($httpAdapter, $settingsConfig);
  }
}

?>
