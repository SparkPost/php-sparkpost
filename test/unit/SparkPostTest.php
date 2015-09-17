<?php
namespace SparkPost\Test;

use SparkPost\SparkPost;
use Ivory\HttpAdapter\CurlHttpAdapter;

class SparkPostTest extends \PHPUnit_Framework_TestCase {

  /**
	 * Allows access to private properties in the Transmission class
	 *
	 * @param string $name
	 * @param {*}
	 * @return ReflectionMethod
	 */
	private static function setPrivateProperty($name, $value) {
		$class = new \ReflectionClass('\SparkPost\SparkPost');
		$prop = $class->getProperty($name);
		$prop->setAccessible(true);
		$prop->setValue($value);
	}


  public function setUp() {
    $this->setPrivateProperty('config', null);
    $this->setPrivateProperty('httpAdapter', null);
	}

	/**
	 * @desc Ensures that the configuration class is not instantiable.
	 */
	public function testConstructorCannotBeCalled() {
		$class = new \ReflectionClass('\SparkPost\SparkPost');
		$this->assertFalse($class->isInstantiable());
	}

	/**
	 * @desc Tests that an exception is thrown when a library tries to recieve the config and it has not yet been set.
	 * 		Since its a singleton this test must come before any setConfig tests.
	 * @expectedException Exception
	 * @expectedExceptionMessage No configuration has been provided
	 */
	public function testGetConfigEmptyException() {
		SparkPost::unsetConfig();
		SparkPost::getConfig();
	}

	/**
	 * @desc Tests that the api key is set when setting the config
	 * @expectedException Exception
	 * @expectedExceptionMessage You must provide an API key
	 */
	public function testSetConfigAPIKeyNotSetException() {
		SparkPost::setConfig(['something'=>'other than an API Key']);
	}

	/**
	 * @desc Tests that the api key is set when setting the config and that its not empty
	 * @expectedException Exception
	 * @expectedExceptionMessage You must provide an API key
	 */
	public function testSetConfigAPIKeyEmptyException() {
		SparkPost::setConfig(['key'=>'']);
	}

	/**
	 * @desc Tests overridable values are set while invalid values are ignored
	 */
	public function testSetConfigMultipleValuesAndGetConfig() {
		SparkPost::setConfig(['key'=>'lala', 'version'=>'v8', 'port'=>1024, 'someOtherValue'=>'fakeValue']);

		$testConfig = SparkPost::getConfig();
		$this->assertEquals('lala', $testConfig['key']);
		$this->assertEquals('v8', $testConfig['version']);
		$this->assertEquals(1024, $testConfig['port']);
		$this->assertNotContains('someOtherValue', array_keys($testConfig));
		$this->assertEquals('https', $testConfig['protocol']);
		$this->assertEquals('api.sparkpost.com', $testConfig['host']);
		$this->assertEquals(true, $testConfig['strictSSL']);
	}

  /**
   * @desc tests getting an unset
   * @expectedException Exception
   * @expectedExceptionMessageRegExp /No Http Adapter/
   */
  public function testGetHttpAdapterForIsset() {
    SparkPost::getHttpAdapter();
  }

  /**
   * @desc tests failing validation for http adapters
   * @expectedException Exception
   * @expectedExceptionMessageRegExp /must be a valid Ivory\\HttpAdapter/
   */
  public function testSetInvalidHttpAdapter() {
    SparkPost::setHttpAdapter(new \stdClass());
  }

  public function testSetAndGetValidHttpAdapter() {
    SparkPost::setConfig(['key'=>'lala']);
    SparkPost::setHttpAdapter(new CurlHttpAdapter());
    $this->assertEquals('Ivory\HttpAdapter\CurlHttpAdapter', get_class(Sparkpost::getHttpAdapter()));
  }

  public function testConfigure() {
    SparkPost::configure(new CurlHttpAdapter(), ['key'=>'lala']);
    $this->assertEquals('Ivory\HttpAdapter\CurlHttpAdapter', get_class(Sparkpost::getHttpAdapter()));
  }

  public function testDefaultHeaders() {
    $key = 'lala';
    SparkPost::setConfig(['key'=>$key]);
    $this->assertEquals($key, Sparkpost::getHttpHeaders()['Authorization']);
    $this->assertEquals('application/json', Sparkpost::getHttpHeaders()['Content-Type']);
  }

  public function testOverrideDefaultHeaders() {
    $key = 'lala';
    $headers=['Content-Type'=>'my/type'];
    SparkPost::setConfig(['key'=>$key]);
    $this->assertEquals($key, Sparkpost::getHttpHeaders($headers)['Authorization']);
    $this->assertEquals('my/type', Sparkpost::getHttpHeaders($headers)['Content-Type']);
  }

}
?>
