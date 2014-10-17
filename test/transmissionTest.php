<?php
require_once 'vendor/autoload.php';

use MessageSystems\Transmission;
use MessageSystems\Configuration;

class TransmissionTest extends PHPUnit_Framework_TestCase {
	
	private $transmission = null;
	
	private static function getMethod($name) {
		$class = new ReflectionClass('\MessageSystems\Transmission');
		$method = $class->getMethod($name);
		$method->setAccessible(true);
		return $method;
	}
	
	/**
	 * (non-PHPdoc)
	 * @before
	 * @see PHPUnit_Framework_TestCase::setUp()
	 */
	public function setUp() {
		Configuration::setConfig(['key'=>'blah']);
		$this->transmission = new Transmission(); 
	}
	
	public function testFetch() {
		$this->assertEquals('', self::getMethod('fetch')->invokeArgs($this->transmission, [null]));
	}
}
?>