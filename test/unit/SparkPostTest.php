<?php
namespace SparkPost\Test;

use Ivory\HttpAdapter\CurlHttpAdapter;
use Mockery;
use SparkPost\SparkPost;
use SparkPost\Test\TestUtils\ClassUtils;

class SparkPostTest extends \PHPUnit_Framework_TestCase {

  private static $utils;
  private $adapterMock;
  /** @var SparkPost */
  private $resource;

  /**
   * (non-PHPdoc)
   * @before
   * @see PHPUnit_Framework_TestCase::setUp()
   */
  public function setUp() {
    //setup mock for the adapter
    $this->adapterMock = Mockery::mock('Ivory\HttpAdapter\HttpAdapterInterface', function($mock) {
      $mock->shouldReceive('setConfiguration');
      $mock->shouldReceive('getConfiguration->getUserAgent')->andReturn('php-sparkpost/0.2.0');
    });

    $this->resource = new SparkPost($this->adapterMock, ['key'=>'a key']);
    self::$utils = new ClassUtils($this->resource);
    self::$utils->setProperty($this->resource, 'httpAdapter', $this->adapterMock);
  }

  public function tearDown(){
    Mockery::close();
  }

  /**
   * @desc Ensures that the configuration class is not instantiable.
   */
  public function testConstructorSetsUpTransmissions() {
    $sparky = new SparkPost(new CurlHttpAdapter(), ['key'=>'a key']);
    $this->assertEquals('SparkPost\Transmission', get_class($sparky->transmission));
    $adapter = self::$utils->getProperty($this->resource, 'httpAdapter');
    $this->assertRegExp('/php-sparkpost.*/', $adapter->getConfiguration()->getUserAgent());
  }

  public function testSetConfigStringKey() {
    $this->resource->setConfig('a key');
    $config = self::$utils->getProperty($this->resource, 'config');
    $this->assertEquals('a key', $config['key']);
  }

  /**
   * @expectedException Exception
   * @expectedExceptionMessageRegExp /API key/
   */
  public function testSetBadConfig() {
    $this->resource->setConfig(['not'=>'a key']);
  }


  public function testGetHeaders() {
    $results = $this->resource->getHttpHeaders();
    $this->assertEquals('a key', $results['Authorization']);
    $this->assertEquals('application/json', $results['Content-Type']);
  }

  public function testSetUnwrapped() {
    $results = $this->resource->setupUnwrapped('ASweetEndpoint');
    $this->assertEquals($this->resource->ASweetEndpoint, $results);
    $this->assertInstanceOf('SparkPost\APIResource', $results);
    $this->assertEquals('ASweetEndpoint', $results->endpoint);
  }

}
?>
