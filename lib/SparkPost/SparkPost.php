<?php
namespace SparkPost;

class SparkPost {
	
	private static $config;
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
	public static function setConfig(array $configMap) {
		//check for API key because its required	
		if (!isset($configMap['key']) || empty(trim($configMap['key']))){
			throw new \Exception('You must provide an API key');
		}
		self::$config = self::$defaults;
		foreach ($configMap as $configOption => $configValue) {
			if(key_exists($configOption, self::$config)) {
				self::$config[$configOption] = $configValue;
			}
		}
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
}

?>