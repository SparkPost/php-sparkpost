<?php

require_once 'vendor/autoload.php'; // Autoload files using Composer autoload

use MessageSystems\Transmission;
use MessageSystems\Configuration;

class SparkPost {
	
	public $transmission;
	
	public function __construct($globalOpts) {
		Configuration::setConfig($globalOpts);
		$this->transmission = new Transmission(); 
	}
}
?>