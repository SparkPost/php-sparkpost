<?php
namespace SparkPost\Test;

use SparkPost\APIResource;
use SparkPost\SparkPost;
use Guzzle\Plugin\Mock\MockPlugin;
use Guzzle\Http\Message\Response;


class APIResourceTest extends \PHPUnit_Framework_TestCase {
	
	private $client = null;
	
	/**
	 * Allows access to private methods
	 * 
	 * This is needed to mock the GuzzleHttp\Client responses
	 * 
	 * @param string $name
	 * @return ReflectionMethod
	 */
	private static function getMethod($name) {
		$class = new \ReflectionClass('\SparkPost\APIResource');
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
		SparkPost::setConfig(array('key'=>'blah')); 
		$this->client = self::getMethod('getHttpClient')->invoke(null); //so we can bootstrap api responses
		APIResource::$endpoint = 'someValidEndpoint'; // when using APIResource directly an endpoint needs to be set.
	}
	
	/**
	 * @desc Ensures that the configuration class is not instantiable.
	 */
	public function testConstructorCannotBeCalled() {
		$class = new \ReflectionClass('\SparkPost\Transmission');
		$this->assertFalse($class->isInstantiable());
	}
	
	/**
	 * @desc tests happy path
	 */
	public function testFetchWithGoodResponse() {
		$mock = new MockPlugin();
		$mock->addResponse(new Response(200, array(), '{"results":[{"test":"This is a test"}, {"test":"two"}]}'));
		$this->client->addSubscriber($mock);
		$this->assertEquals(array("results"=>array(array('test'=>'This is a test'), array('test'=>'two'))), APIResource::fetchResource());
	}
	
	/**
	 * @desc tests happy path
	 */
	public function testDeleteWithGoodResponse() {
		$mock = new MockPlugin();
		$mock->addResponse(new Response(200, array(), '{"results":[{"test":"This is a test"}]}'));
		$this->client->addSubscriber($mock);
		
		$this->assertEquals(array("results"=>array(array('test'=>'This is a test'))), APIResource::deleteResource('someId'));
	}
	
	/**
	 * @desc tests 404 bad response
	 * @expectedException Exception
	 * @expectedExceptionMessage The specified resource does not exist
	 */
	public function testFetchWith404Response() {
		$mock = new MockPlugin();
		$mock->addResponse(new Response(404, array()));
		$this->client->addSubscriber($mock);
		APIResource::fetchResource('someId');
	}
	
	/**
	 * @desc tests unknown bad response
	 * @expectedException Exception
	 * @expectedExceptionMessage Received bad response from SomeValidEndpoint API: 400
	 */
	public function testFetchWithOtherBadResponse() {
		$mock = new MockPlugin();
		$mock->addResponse(new Response(400, array()));
		$this->client->addSubscriber($mock);
		APIResource::fetchResource('someId');
	}
	
	/**
	 * @desc tests bad response
	 * @expectedException Exception
	 * @expectedExceptionMessageRegExp /Unable to contact SomeValidEndpoint API:.* /
	 */
	public function testFetchForCatchAllException() {
		$mock = new MockPlugin();
		$mock->addResponse(new Response(500));
		$this->client->addSubscriber($mock);
		APIResource::fetchResource('someId');
	}
	
	/**
	 * @desc tests happy path
	 */
	public function testSuccessfulSend() {
		$body = array("result"=>array("transmission_id"=>"11668787484950529"), "status"=>array("message"=> "ok","code"=> "1000"));
		$mock = new MockPlugin();
		$mock->addResponse(new Response(200, array(), json_encode($body)));
		$this->client->addSubscriber($mock);
		
		
		$this->assertEquals($body, APIResource::sendRequest(array('text'=>'awesome email')));
	}
	
	/**
	 * @desc tests bad response
	 * @expectedException Exception
	 * @expectedExceptionMessage ["This is a fake error"]
	 */
	public function testSendFor400Exception() {
		$body = array('errors'=>array('This is a fake error'));
		$mock = new MockPlugin();
		$mock->addResponse(new Response(400, array(), json_encode($body)));
		$this->client->addSubscriber($mock);
		APIResource::sendRequest(array('text'=>'awesome email'));
	}
	
	
	/**
	* @desc tests bad response
	* @expectedException Exception
	* @expectedExceptionMessageRegExp /Unable to contact SomeValidEndpoint API:.* /
	*/
	public function testSendForCatchAllException() {
		$mock = new MockPlugin();
		$mock->addResponse(new Response(500));
		$this->client->addSubscriber($mock);
		APIResource::sendRequest(array('text'=>'awesome email'));
	}
	
}
