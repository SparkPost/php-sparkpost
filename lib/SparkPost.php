<?php
namespace SparkPost;

use MessageSystems\Transmission;
use MessageSystems\Configuration;

class SparkPost {
	
	public $transmission;
	
	public function __construct($globalOpts) {
		Configuration::setConfig($globalOpts);
	}
	
	public function Transmission(Array $options = null) {
		$this->transmission = new Transmission($options);
		return $this->transmission;
	}
}
?>