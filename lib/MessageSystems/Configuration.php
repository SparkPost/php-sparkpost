<?php
namespace MessageSystems;

class Configuration {
	
	private static $config;
	private static $defaults = [
			'host'=>'app.cloudplaceholder.com',
			'protocol'=>'https',
			'port'=>443,
			'strictSSL'=>true,
			'key'=>'',
			'version'=>'v1'	
		];
	
	
	private function __constructor(){
	}
	
	public static function setConfig($configMap) {
		//check for API key because its required	
		if (!isset($configMap['key']) || empty(trim($configMap['key']))){
			throw new \Exception('You must provide an API key');
		}
		self::$config = self::$defaults;
		foreach ($configMap as $configOption => $configValue) {
			self::$config[$configOption] = $configValue;
		}
	}
	
	public static function getConfig() {
		if (self::$config === null) {	
			throw new \Exception('No configuration has been provided');
		}
		return self::$config;
	}
}

?>