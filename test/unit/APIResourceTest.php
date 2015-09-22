<?php
namespace SparkPost\Test;
use SparkPost\APIResource;
use SparkPost\Test\TestUtils\ClassUtils;
use Ivory\HttpAdapter\CurlHttpAdapter;

class APIResourceTest extends \PHPUnit_Framework_TestCase {

  private static $utils;
  private $adapterMock;
  private $resource;

  public static function setUpBeforeClass() {

  }

	/**
	 * (non-PHPdoc)
	 * @before
	 * @see PHPUnit_Framework_TestCase::setUp()
	 */
	public function setUp() {
    $this->adapterMock = $this->getMockBuilder('CurlHttpAdapter')->getMock();

    $this->resource = new APIResource(new CurlHttpAdapter(), ['key'=>'a key']);
    self::$utils = new ClassUtils($this->resource);
  }

  public function testConstructorSetsUpAdapterAndConfig() {
    $this->assertEquals('Ivory\HttpAdapter\CurlHttpAdapter', get_class(self::$utils->getProperty($this->resource, 'httpAdapter')));
  }

}
