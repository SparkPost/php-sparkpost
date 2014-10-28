<?php
namespace SparkPost\Test;

use MessageSystems\Transmission;
use MessageSystems\Configuration;
use GuzzleHttp\Subscriber\Mock;
use GuzzleHttp\Message\Response;
use GuzzleHttp\Stream\Stream;


/**
 * 
 *
 */
class TransmissionTest extends \PHPUnit_Framework_TestCase {
	
	private $transmission = null;
	private $client = null;
	
	private static function getMethod($name) {
		$class = new \ReflectionClass('\MessageSystems\Transmission');
		$method = $class->getMethod($name);
		$method->setAccessible(true);
		return $method;
	}
	
	private static function getProperty($obj, $name) {
		$class = new \ReflectionClass('\MessageSystems\Transmission');
		$property = $class->getProperty($name);
		$property->setAccessible(true);
		return $property->getValue($obj);
	}
	
	/**
	 * Dynamically dereferences an array for a path list provided by the $dereferenceArray
	 * @param array $array Nested array to be dereferenced
	 * @param array $dereferenceArray list of key values to dereference
	 * @return mixed
	 */
	private static function dereference($array, $dereferenceArray) {
		$value = $array;
		foreach($dereferenceArray as $derefValue) {
			$value = $value[$derefValue];
		}
		return $value;
	}
	
	/**
	 * (non-PHPdoc)
	 * @before
	 * @see PHPUnit_Framework_TestCase::setUp()
	 */
	public function setUp() {
		Configuration::setConfig(['key'=>'blah']);
		$this->transmission = new Transmission();
		$this->client = self::getProperty($this->transmission, 'request'); //so we can bootstrap api responses
	}
	
	/**
	 * @desc Constructor will not set invalid keys
	 */
	public function testConstructorWillNotSetInvalidKeys() {
		$transmission = new Transmission(['blah'=>'blah']);
		$this->assertArrayNotHasKey('blah', $this->getProperty($transmission, 'model'));
	}
	
	/**
	 * 
	 */
	public function testConstructorWillSetValidKeys() {
		$transmission = new Transmission(['description'=>'this is a test', 'text'=>'test txt', 'openTracking'=>false, 'clickTracking'=>false, 'useDraftTemplate'=>false, 'recipientList'=>'my recip list']);
		$this->assertArrayHasKey('description', $this->getProperty($transmission, 'model'));
		$this->assertEquals('this is a test', $this->getProperty($transmission, 'model')['description']);
		$this->assertEquals('test txt', $this->getProperty($transmission, 'model')['content']['text']);
		$this->assertEquals(false, $this->getProperty($transmission, 'model')['options']['open_tracking']);
		$this->assertEquals(false, $this->getProperty($transmission, 'model')['options']['click_tracking']);
		$this->assertEquals(false, $this->getProperty($transmission, 'model')['content']['use_draft_template']);
		$this->assertEquals('my recip list', $this->getProperty($transmission, 'model')['recipients']['list_id']);
	}
	
	/**
	 * 
	 */
	public function testAllWithGoodResponse() {
		$mock = new Mock([new Response(200, [], Stream::factory('{"results":[{"test":"This is a test"}, {"test":"two"}]}'))]);
		$this->client->getEmitter()->attach($mock);
		$this->assertEquals(["results"=>[['test'=>'This is a test'], ['test'=>'two']]], $this->transmission->all());
	}
	
	/**
	 * 
	 */
	public function testFindWithGoodResponse() {
		$mock = new Mock([new Response(200, [], Stream::factory('{"results":[{"test":"This is a test"}]}'))]);
		$this->client->getEmitter()->attach($mock);
		$this->assertEquals(["results"=>[['test'=>'This is a test']]], $this->transmission->find('someId'));
	}
	
	/**
	 * @expectedException Exception
	 * @expectedExceptionMessage The specified Transmission ID does not exist
	 */
	public function testFindWith404Response() {
		$mock = new Mock([new Response(404, [])]);
		$this->client->getEmitter()->attach($mock);
		$this->transmission->find('someId');
	}
	
	/**
	 * @expectedException Exception
	 * @expectedExceptionMessage Received bad response from Transmission API: 400
	 */
	public function testFindWithOtherBadResponse() {
		$mock = new Mock([new Response(400, [])]);
		$this->client->getEmitter()->attach($mock);
		$this->transmission->find('someId');
	}
	
// 	/**
// 	 * @expectedException Exception
// 	 * @expectedExceptionMessageRegExp /Unable to contact Transmissions API:.* /
// 	 */
// 	public function testFindForUnableToContactServer() {
// 		$mock = new Mock([new Response(500)]);
// 		$this->client->getEmitter()->attach($mock);
// 		$this->transmission->find('someId');
// 	}
	
	
	/**
	 * 
	 */
	public function testSuccessfulSend() {
		$body = ["result"=>["transmission_id"=> "11668787484950529"], "status"=>["message"=> "ok","code"=> "1000"]];
		$mock = new Mock([new Response(200, [], Stream::factory(json_encode($body)))]);
		$this->client->getEmitter()->attach($mock);
		$this->assertEquals($body, $this->transmission->send());	
	}
	
	/**
	 * @expectedException Exception
	 * @expectedExceptionMessage This is a fake error
	 */
	public function testFindForRequestException() {
		$body = ['errors'=>['This is a fake error']];
		$mock = new Mock([new Response(400, [], Stream::factory(json_encode($body)))]);
		$this->client->getEmitter()->attach($mock);
		$this->transmission->send();
	}
	
// 	/**
// 	 * @expectedException Exception
// 	 * @expectedExceptionMessageRegExp /Unable to contact Traissions API: \w+/
// 	 */
// 	public function testSendForUnableToContactServer() {
// 		$mock = new Mock([new Response(500, [])]);
// 		$this->client->getEmitter()->attach($mock);
// 		$this->transmission->send();
// 	}
	
	
	public function setProvider() {
		return [
			['setMetadata', ['metadata']],
			['setSubstitutionData', ['substitution_data']],
			['setCampaign', ['campaign_id']],
			['setDescription', ['description']],
			['setReturnPath', ['return_path']],
			['setReplyTo', ['content', 'reply_to']],
			['setSubject', ['content', 'subject']],
			['setFrom', ['content', 'from']],
			['setHTMLContent', ['content', 'html']],
			['setTextContent', ['content', 'text']],
			['setRfc822Content', ['content', 'email_rfc822']],
			
			['useRecipientList', ['recipients', 'list_id']],
			['useStoredTemplate', ['content', 'template_id']]
		];
	}
	
	/**
	 * @dataProvider setProvider
	 */
	public function testSimpleSetFunctions($setterFunction, $dereference) {
		$testValue = 'test';
		$returnValue = $this->transmission->$setterFunction($testValue);
		$this->assertInstanceOf('MessageSystems\Transmission', $returnValue);
		$this->assertSame($this->transmission, $returnValue);
		$this->assertEquals($testValue, self::dereference(self::getProperty($this->transmission, 'model'), $dereference));
	}
	
	public function complexSetFunctionsProvider() {
		return [
			['addRecipient', ['recipients'], ['address'=>'testRecipient@example.com'], [['address'=>'testRecipient@example.com']]],
			['addRecipients', ['recipients'], [
					['address'=>'testRecipient1@example.com'], 
					['address'=>'testRecipient2@example.com'], 
					['address'=>'testRecipient3@example.com']
			], 
			[
					['address'=>'testRecipient1@example.com'], 
					['address'=>'testRecipient2@example.com'], 
					['address'=>'testRecipient3@example.com']	
			]],
			['setContentHeaders', ['content', 'headers'], ['x-head'=>'somevalue'], ['x-head'=>'somevalue']],
		];
	}
	
	/**
	 * @dataProvider complexSetFunctionsProvider
	 */
	public function testComplexSetFunctions($setterFunction, $dereference, $setValue, $expected) {
		$returnValue = $this->transmission->$setterFunction($setValue);
		$this->assertInstanceOf('MessageSystems\Transmission', $returnValue);
		$this->assertSame($this->transmission, $returnValue);
		$this->assertEquals($expected, self::dereference(self::getProperty($this->transmission, 'model'), $dereference));
	}

	
	
	public function optionsProvider() {
		return [
			['enableClickTracking', ['options', 'click_tracking'], true],
			['disableClickTracking', ['options', 'click_tracking'], false],
			['enableOpenTracking', ['options', 'open_tracking'], true],
			['disableOpenTracking', ['options', 'open_tracking'], false],
			['useDraftTemplate', ['content', 'use_draft_template'], true],
			['usePublishedTemplate', ['content', 'use_draft_template'], false]
		];
	}
	
	/**
	 * @dataProvider optionsProvider
	 */
	public function testOptionsFunctions($setterFunction, $dereference, $expected) {
		$returnValue = $this->transmission->$setterFunction();
		$this->assertInstanceOf('MessageSystems\Transmission', $returnValue);
		$this->assertSame($this->transmission, $returnValue);
		$this->assertEquals($expected, self::dereference(self::getProperty($this->transmission, 'model'), $dereference));
	}
	
}
?>