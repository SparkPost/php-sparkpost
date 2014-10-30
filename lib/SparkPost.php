<?php
namespace SparkPost;

use MessageSystems\Transmission;
use MessageSystems\Configuration;

class SparkPost {
	
	public $transmission;
	
	/**
	 * @desc Sets up the config for the sdk suite
	 * @param array $globalOpts
	 */
	public function __construct(array $globalOpts) {
		Configuration::setConfig($globalOpts);
	}
	
	/**
	 * @desc Creates a new Transmission object and returns it.
	 * @param array $options Transmission constructor options
	 * @see \MessageSystems\Transmission
	 * @return \MessageSystems\Transmission
	 */
	public function Transmission(array $options = null) {
		$this->transmission = new Transmission($options);
		return $this->transmission;
	}
}
?>