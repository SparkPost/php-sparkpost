<?php
namespace SparkPost\Test;

use SparkPost\Transmission;
use SparkPost\SparkPost;
use Guzzle\Plugin\Mock\MockPlugin;
use Guzzle\Http\Message\Response;


class TransmissionTest extends \PHPUnit_Framework_TestCase {
	
	private $client = null;
	
	/**
	 * Allows access to private methods in the Transmission class
	 * 
	 * This is needed to mock the GuzzleHttp\Client responses
	 * 
	 * @param string $name
	 * @return ReflectionMethod
	 */
	private static function getMethod($name) {
		$class = new \ReflectionClass('\SparkPost\Transmission');
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
	public function testAllWithGoodResponse() {
		$mock = new MockPlugin();
		$mock->addResponse(new Response(200, array(), '{"results":[{"test":"This is a test"}, {"test":"two"}]}'));
		$this->client->addSubscriber($mock);
		$this->assertEquals(array("results"=>array(array('test'=>'This is a test'), array('test'=>'two'))), Transmission::all());
	}
	
	/**
	 * @desc tests happy path
	 */
	public function testFindWithGoodResponse() {
		$mock = new MockPlugin();
		$mock->addResponse(new Response(200, array(), '{"results":[{"test":"This is a test"}]}'));
		$this->client->addSubscriber($mock);
		
		$this->assertEquals(array("results"=>array(array('test'=>'This is a test'))), Transmission::find('someId'));
	}
	
	/**
	 * @desc tests 404 bad response
	 * @expectedException Exception
	 * @expectedExceptionMessage The specified resource does not exist
	 */
	public function testFindWith404Response() {
		$mock = new MockPlugin();
		$mock->addResponse(new Response(404, array()));
		$this->client->addSubscriber($mock);
		Transmission::find('someId');
	}
	
	/**
	 * @desc tests unknown bad response
	 * @expectedException Exception
	 * @expectedExceptionMessage Received bad response from Transmissions API: 400
	 */
	public function testFindWithOtherBadResponse() {
		$mock = new MockPlugin();
		$mock->addResponse(new Response(400, array()));
		$this->client->addSubscriber($mock);
		Transmission::find('someId');
	}
	
	/**
	 * @desc tests bad response
	 * @expectedException Exception
	 * @expectedExceptionMessageRegExp /Unable to contact Transmissions API:.* /
	 */
	public function testFindForCatchAllException() {
		$mock = new MockPlugin();
		$mock->addResponse(new Response(500));
		$this->client->addSubscriber($mock);
		Transmission::find('someId');
	}
	
	/**
	 * @desc tests happy path
	 */
	public function testSuccessfulSend() {
		$body = array("result"=>array("transmission_id"=>"11668787484950529"), "status"=>array("message"=> "ok","code"=> "1000"));
		$mock = new MockPlugin();
		$mock->addResponse(new Response(200, array(), json_encode($body)));
		$this->client->addSubscriber($mock);
		
		
		$this->assertEquals($body, Transmission::send(array('text'=>'awesome email')));
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
		Transmission::send(array('text'=>'awesome email'));
	}
	
	
	/**
	* @desc tests bad response
	* @expectedException Exception
	* @expectedExceptionMessageRegExp /Unable to contact Transmissions API:.* /
	*/
	public function testSendForCatchAllException() {
		$mock = new MockPlugin();
		$mock->addResponse(new Response(500));
		$this->client->addSubscriber($mock);
		Transmission::send(array('text'=>'awesome email'));
	}
	
}
?>