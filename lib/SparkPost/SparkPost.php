<?php
namespace SparkPost;
use Ivory\HttpAdapter;
use Ivory\HttpAdapter\Configuration;

class SparkPost {

	private static $config;
	private static $httpAdapter;

	private static $defaults = array(
			'host'=>'api.sparkpost.com',
			'protocol'=>'https',
			'port'=>443,
			'strictSSL'=>true,
			'key'=>'',
			'version'=>'v1'
		);

	/**
	 * Enforce that this object can't be instansiated
	 */
	private function __construct(){}

	/**
	 * Allows the user to pass in values to override the defaults and set their API key
	 * @param Array $configMap - Hashmap that contains config values for the SDK to connect to SparkPost
	 * @throws \Exception
	 */
	public static function setConfig($httpAdapter, array $configMap) {
		//check for API key because its required
		if (isset($configMap['key'])){
			$key = trim($configMap['key']);
			if(empty($key)){
				throw new \Exception('You must provide an API key');
			}
		} else {
			throw new \Exception('You must provide an API key');
		}
		// TODO: need to figure out how to enforce this
		// if (!$httpAdapter instanceOf HttpAdapterInterface) {
		// 	throw new \Exception('First Argument must be a valid Ivory\HttpAdapter');
		// }

		self::$config = self::$defaults;

		self::$httpAdapter = $httpAdapter;

		foreach ($configMap as $configOption => $configValue) {
			if(key_exists($configOption, self::$config)) {
				self::$config[$configOption] = $configValue;
			}
		}

		self::$httpAdapter->setConfiguration(self::getHttpConfig(self::$config));
	}


	/**
	 * @desc Merges passed in headers with default headers for http requests
	 * @return Array - headers to be set on http requests
	 */
	public static function getHttpHeaders(Array $headers = null) {
		$defaultOptions = [
			'Authorization' => self::$config['key'],
			'Content-Type' => 'application/json',
		];

		// Merge passed in headers with defaults
		if (!is_null($headers)) {
			foreach ($headers as $header => $value) {
				$defaultOptions[$header] = $value;
			}
		}
		return $defaultOptions;
	}


	/**
	 * @desc Helper function for getting the configuration for http requests
	 * @return \Ivory\HttpAdapter\Configuration
	 */
	 // TODO: Need to figure out how to set strictSSL
	private static function getHttpConfig($config) {
		// get composer.json to extract version number
		$composerFile = file_get_contents(dirname(__FILE__) . "/../../composer.json");
		$composer = json_decode($composerFile, true);

		// create Configuration for http adapter
		$httpConfig = new Configuration();
		$baseUrl = $config['protocol'] . '://' . $config['host'] . ($config['port'] ? ':' . $config['port'] : '') . '/api/' . $config['version'];
		$httpConfig->setBaseUri($baseUrl);
		$httpConfig->setUserAgent('php-sparkpost/' . $composer['version']);
		return $httpConfig;
	}

	/**
	 * Retrieves the configuration that was previously setup by the user
	 * @throws \Exception
	 */
	public static function getConfig() {
		if (self::$config === null) {
			throw new \Exception('No configuration has been provided');
		}
		return self::$config;
	}

	public static function unsetConfig() {
		self::$config = NULL;
	}

	/**
	 * Retrieves the Http Adapter that was previously setup by the user
	 * @throws \Exception
	 */
	public static function getHttpAdapter() {
		if (self::$config === null) {
			throw new \Exception('No Http Adapter has been provided');
		}
		return self::$httpAdapter;
	}
}

?>
