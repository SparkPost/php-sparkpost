<?php
namespace SparkPost\Test;

use MessageSystems\Transmission;
use MessageSystems\SparkPost;
use GuzzleHttp\Subscriber\Mock;
use GuzzleHttp\Message\Response;
use GuzzleHttp\Stream\Stream;


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
		$class = new \ReflectionClass('\MessageSystems\Transmission');
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
		SparkPost::setConfig(['key'=>'blah']); 
		$this->client = self::getMethod('getHttpClient')->invoke(null); //so we can bootstrap api responses
	}
	
	/**
	 * @desc Ensures that the configuration class is not instantiable.
	 */
	public function testConstructorCannotBeCalled() {
		$class = new \ReflectionClass('\MessageSystems\Transmission');
		$this->assertFalse($class->isInstantiable());
	}
	
	/**
	 * @desc tests happy path
	 */
	public function testAllWithGoodResponse() {
		$mock = new Mock([new Response(200, [], Stream::factory('{"results":[{"test":"This is a test"}, {"test":"two"}]}'))]);
		$this->client->getEmitter()->attach($mock);
		$this->assertEquals(["results"=>[['test'=>'This is a test'], ['test'=>'two']]], Transmission::all());
		$this->client->getEmitter()->detach($mock);
	}
	
	/**
	 * @desc tests happy path
	 */
	public function testFindWithGoodResponse() {
		$mock = new Mock([new Response(200, [], Stream::factory('{"results":[{"test":"This is a test"}]}'))]);
		$this->client->getEmitter()->attach($mock);
		$this->assertEquals(["results"=>[['test'=>'This is a test']]], Transmission::find('someId'));
		$this->client->getEmitter()->detach($mock);
	}
	
	/**
	 * @desc tests 404 bad response
	 */
	public function testFindWith404Response() {
		$mock = new Mock([new Response(404, [])]);
		$this->client->getEmitter()->attach($mock);
		try {
			Transmission::find('someId');
		} catch (\Exception $e) {
			$this->assertEquals('The specified Transmission ID does not exist', $e->getMessage());
		} finally {
			$this->client->getEmitter()->detach($mock);
		}
	}
	
	/**
	 * @desc tests unknown bad response
	 */
	public function testFindWithOtherBadResponse() {
		$mock = new Mock([new Response(400, [])]);
		$this->client->getEmitter()->attach($mock);
		try {
			Transmission::find('someId');
		} catch (\Exception $e) {
			$this->assertEquals('Received bad response from Transmission API: 400', $e->getMessage());
		} finally {
			$this->client->getEmitter()->detach($mock);
		}
	}
	
	/**
	 * @desc tests happy path
	 */
	public function testSuccessfulSend() {
		$body = ["result"=>["transmission_id"=> "11668787484950529"], "status"=>["message"=> "ok","code"=> "1000"]];
		$mock = new Mock([new Response(200, [], Stream::factory(json_encode($body)))]);
		$this->client->getEmitter()->attach($mock);
		$this->assertEquals($body, Transmission::send(['text'=>'awesome email']));
		$this->client->getEmitter()->detach($mock);
	}
	
	/**
	 * @desc tests bad response
	 */
	public function testSendForRequestException() {
		$body = ['errors'=>['This is a fake error']];
		$mock = new Mock([new Response(400, [], Stream::factory(json_encode($body)))]);
		$this->client->getEmitter()->attach($mock);
		try {
			Transmission::send(['text'=>'awesome email']);
		} catch (\Exception $e) {
			$this->assertEquals('["This is a fake error"]', $e->getMessage());
		} finally {
			$this->client->getEmitter()->detach($mock);
		}
	}
	
}
?>