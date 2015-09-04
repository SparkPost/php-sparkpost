<?php
namespace SparkPost\Test;

use SparkPost\Transmission;
use SparkPost\SparkPost;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;


class TransmissionTest extends \PHPUnit_Framework_TestCase {

	private $mock = null;

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
	 * Allows access to private properties in the Transmission class
	 *
	 * This is needed to mock the GuzzleHttp\Client responses
	 *
	 * @param string $name
	 * @param {*}
	 * @return ReflectionMethod
	 */
	private static function getProperty($name) {
		$class = new \ReflectionClass('\SparkPost\Transmission');
		$prop = $class->getProperty($name);
		$prop->setAccessible(true);
		return $prop->getValue();
	}

	/**
	 * Allows access to private properties in the Transmission class
	 *
	 * This is needed to mock the GuzzleHttp\Client responses
	 *
	 * @param string $name
	 * @param {*}
	 * @return ReflectionMethod
	 */
	private static function setPrivateProperty($name, $value) {
		$class = new \ReflectionClass('\SparkPost\Transmission');
		$prop = $class->getProperty($name);
		$prop->setAccessible(true);
		$prop->setValue($value);
	}

	/**
	 * (non-PHPdoc)
	 * @before
	 * @see PHPUnit_Framework_TestCase::setUp()
	 */
	public function setUp() {
		SparkPost::setConfig(['key'=>'blah']);
		$this->mock = new MockHandler([]);
		$handler = HandlerStack::create($this->mock);
		self::setPrivateProperty('request', new Client(['handler' => $handler]));
	}

	/**
	 * @desc tests the initial instantiaion of the guzzle client
	 */
	public function testGetHttpClient() {
		// need to un-set it from the setup above
		self::setPrivateProperty('request', null);

		self::getMethod('getHttpClient')->invoke(null);
		$requestObj = self::getProperty('request');
		$this->assertInstanceOf('GuzzleHttp\Client', $requestObj);
		$config = $requestObj->getConfig();
		$this->assertRegExp('/php-sparkpost\/\d\.\d\.\d/', $config['headers']['User-Agent']);

		// make sure that our actual configured instance isn't being used
		self::setPrivateProperty('request', null);
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
		$body = ["results"=>[['test'=>'This is a test'], ['test'=>'two']]];
		$this->mock->append(new Response(200, [], json_encode($body)));
		$this->assertEquals($body, Transmission::all());
	}

	/**
	 * @desc tests happy path
	 */
	public function testFindWithGoodResponse() {
		$body = ["results"=>[['test'=>'This is a test']]];
		$this->mock->append(new Response(200, [], json_encode($body)));
		$this->assertEquals($body, Transmission::find('someId'));
	}

	/**
	 * @desc tests 404 bad response
	 * @expectedException Exception
 	 * @expectedExceptionMessage The specified Transmission ID does not exist
	 */
	public function testFindWith404Response() {
		$this->mock->append(new Response(404, []));
		Transmission::find('someId');
	}

	/**
	 * @desc tests unknown bad response
	 * @expectedException Exception
	 * @expectedExceptionMessage Received bad response from Transmission API: 400
	 */
	public function testFindWithOtherBadResponse() {
		$this->mock->append(new Response(400, []));
		Transmission::find('someId');
	}

	/**
	 * @desc tests bad response
	 * @expectedException Exception
 	 * @expectedExceptionMessageRegExp /Unable to contact Transmissions API:.* /
	 */
	public function testFindForCatchAllException() {
		Transmission::find('someId');
	}

	/**
	 * @desc tests happy path
	 */
	public function testSuccessfulSend() {
		$body = ["result"=>["transmission_id"=>"11668787484950529"], "status"=>["message"=> "ok","code"=> "1000"]];
		$this->mock->append(new Response(200, [], json_encode($body)));
		$this->assertEquals($body, Transmission::send(['text'=>'awesome email']));
	}

	/**
	 * @desc tests bad response
	 * @expectedException Exception
	 * @expectedExceptionMessage ["This is a fake error"]
	 */
	public function testSendFor400Exception() {
		$body = ['errors'=>['This is a fake error']];
		$this->mock->append(new Response(400, [], json_encode($body)));
		Transmission::send(['text'=>'awesome email']);
	}


	/**
	* @desc tests bad response
	* @expectedException Exception
	* @expectedExceptionMessageRegExp /Unable to contact Transmissions API:.* /
	*/
	public function testSendForCatchAllException() {
		Transmission::send(['text'=>'awesome email']);
	}

}
?>
