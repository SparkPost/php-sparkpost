<?php
require_once 'vendor/autoload.php';

use MessageSystems\Transmission;
use MessageSystems\Configuration;
use GuzzleHttp\Client;
use GuzzleHttp\Subscriber\Mock;
use GuzzleHttp\Message\Response;


class TransmissionTest extends PHPUnit_Framework_TestCase {
	
	private $transmission = null;
	private $client = null;
	
	private static function getMethod($name) {
		$class = new ReflectionClass('\MessageSystems\Transmission');
		$method = $class->getMethod($name);
		$method->setAccessible(true);
		return $method;
	}
	
	private static function getProperty($obj, $name) {
		$class = new ReflectionClass('\MessageSystems\Transmission');
		$property = $class->getProperty($name);
		$property->setAccessible(true);
		return $property->getValue($obj);
	}
	
	/**
	 * (non-PHPdoc)
	 * @before
	 * @see PHPUnit_Framework_TestCase::setUp()
	 */
	public function setUp() {
		Configuration::setConfig(['key'=>'blah']);
		$this->transmission = new Transmission();
		$this->client = new Client();
	}
	
	/**
	 * @desc Constructor will not set invalid keys
	 */
	public function testConstructorWillNotSetInvalidKeys() {
		$transmission = new Transmission(['blah'=>'blah']);
		$this->assertArrayNotHasKey('blah', $this->getProperty($transmission, 'model'));
	}
	
	public function testConstructorWillSetValidKeys() {
		$transmission = new Transmission(['description'=>'this is a test', 'text'=>'test txt', 'open_tracking'=>false, 'recipientList'=>'my recip list']);
		$this->assertArrayHasKey('description', $this->getProperty($transmission, 'model'));
		$this->assertEquals('this is a test', $this->getProperty($transmission, 'model')['description']);
		$this->assertEquals('test txt', $this->getProperty($transmission, 'model')['content']['text']);
		$this->assertEquals(false, $this->getProperty($transmission, 'model')['options']['open_tracking']);
		$this->assertEquals('my recip list', $this->getProperty($transmission, 'model')['recipients']['list_name']);
	}
	
	
// 	public function testFetch() {
// 		$mock = new Mock([new Response(200, ['body'=>['test'=>'This is a test']])]);
// 		$this->client->getEmitter()->attach($mock);
// 		$this->assertEquals(['test'=>'This is a test'], self::getMethod('fetch')->invokeArgs($this->transmission, [null]));
// 	}
}
?>