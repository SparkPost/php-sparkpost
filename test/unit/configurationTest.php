<?php
namespace SparkPost\Test;

use MessageSystems\Configuration;

class ConfigurationTest extends \PHPUnit_Framework_TestCase {
	
	/**
	 * @desc Ensures that the configuration class is not instantiable.
	 */
	public function testConstructorCannotBeCalled() {
		$class = new \ReflectionClass('\MessageSystems\Configuration');
		$this->assertFalse($class->isInstantiable()); 
	}
	
	/**
	 * @desc Tests that an exception is thrown when a library tries to recieve the config and it has not yet been set.
	 * 		Since its a singleton this test must come before any setConfig tests.
	 * @expectedException Exception
	 * @expectedExceptionMessage No configuration has been provided
	 */
	public function testGetConfigEmptyException() {
		Configuration::getConfig();
	}
	
	/**
	 * @desc Tests that the api key is set when setting the config
	 * @expectedException Exception
	 * @expectedExceptionMessage You must provide an API key
	 */
	public function testSetConfigAPIKeyNotSetException() {
		Configuration::setConfig(['something'=>'other than an API Key']);
	}
	
	/**
	 * @desc Tests that the api key is set when setting the config and that its not empty
	 * @expectedException Exception
	 * @expectedExceptionMessage You must provide an API key
	 */
	public function testSetConfigAPIKeyEmptyException() {
		Configuration::setConfig(['key'=>'']);
	}
	
	/**
	 * @desc Tests overridable values are set while invalid values are ignored
	 */
	public function testSetConfigMultipleValuesAndGetConfig() {
		Configuration::setConfig(['key'=>'lala', 'version'=>'v8', 'port'=>1024, 'someOtherValue'=>'fakeValue']);
		
		$testConfig = Configuration::getConfig();
		$this->assertEquals('lala', $testConfig['key']);
		$this->assertEquals('v8', $testConfig['version']);
		$this->assertEquals(1024, $testConfig['port']);
		$this->assertNotContains('someOtherValue', array_keys($testConfig));
		$this->assertEquals('https', $testConfig['protocol']);
		$this->assertEquals('app.cloudplaceholder.com', $testConfig['host']);
		$this->assertEquals(true, $testConfig['strictSSL']);
	}
}
?>