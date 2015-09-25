<?php
namespace SparkPost\Test;
use SparkPost\APIResource;
use SparkPost\Test\TestUtils\ClassUtils;
use \Mockery;

class APIResourceTest extends \PHPUnit_Framework_TestCase {

  private static $utils;
  private $adapterMock;
  private $resource;

  private function getExceptionMock($statusCode) {
    $exception = new \Ivory\HttpAdapter\HttpAdapterException();
    $response = Mockery::mock('Ivory\HttpAdapter\Message\ResponseInterface');
    $response->shouldReceive('getStatusCode')->andReturn($statusCode);
    $exception->setResponse($response);
    return $exception;
  }

	/**
	 * (non-PHPdoc)
	 * @before
	 * @see PHPUnit_Framework_TestCase::setUp()
	 */
	public function setUp() {
    $this->adapterMock = Mockery::mock('Ivory\HttpAdapter\HttpAdapterInterface', function($mock) {
      $mock->shouldReceive('setConfiguration');
      $mock->shouldReceive('getConfiguration->getUserAgent')->andReturn('php-sparkpost/0.2.0');
    });
    $this->resource = new APIResource($this->adapterMock, ['key'=>'a key']);
    self::$utils = new ClassUtils($this->resource);

    self::$utils->setProperty($this->resource, 'httpAdapter', $this->adapterMock);
  }

  public function tearDown()
    {
        Mockery::close();
    }

  public function testConstructorSetsUpAdapterAndConfig() {
    $adapter = self::$utils->getProperty($this->resource, 'httpAdapter');
    $this->assertRegExp('/php-sparkpost.*/', $adapter->getConfiguration()->getUserAgent());
  }

  /**
   * @expectedException Exception
   * @expectedExceptionMessageRegExp /valid Ivory\\HttpAdapter/
   */
  public function testSetBadHTTPAdapter() {
    $this->resource->setHttpAdapter(new \stdClass());
  }

  /**
   * @expectedException Exception
   * @expectedExceptionMessageRegExp /API key/
   */
  public function testSetBadConfig() {
    $this->resource->setConfig(['not'=>'a key']);
  }

  public function testCreate() {
    $testInput = ['test'=>'body'];
    $testBody = ["results"=>["my"=>"test"]];
    $responseMock = Mockery::mock();
    $this->adapterMock->shouldReceive('send')->
      once()->
      with(Mockery::type('string'), 'POST', Mockery::type('array'), json_encode($testInput))->
      andReturn($responseMock);
    $responseMock->shouldReceive('getBody->getContents')->andReturn(json_encode($testBody));

    $this->assertEquals($testBody, $this->resource->create($testInput));
  }

  public function testUpdate() {
    $testInput = ['test'=>'body'];
    $testBody = ["results"=>["my"=>"test"]];
    $responseMock = Mockery::mock();
    $this->adapterMock->shouldReceive('send')->
      once()->
      with('/.*\/test/', 'PUT', Mockery::type('array'), json_encode($testInput))->
      andReturn($responseMock);
    $responseMock->shouldReceive('getBody->getContents')->andReturn(json_encode($testBody));

    $this->assertEquals($testBody, $this->resource->update('test', $testInput));
  }

  public function testGet() {
    $testBody = ["results"=>["my"=>"test"]];
    $responseMock = Mockery::mock();
    $this->adapterMock->shouldReceive('send')->
      once()->
      with('/.*\/test/', 'GET', Mockery::type('array'), null)->
      andReturn($responseMock);
    $responseMock->shouldReceive('getBody->getContents')->andReturn(json_encode($testBody));

    $this->assertEquals($testBody, $this->resource->get('test'));
  }

  public function testDelete() {
    $responseMock = Mockery::mock();
    $this->adapterMock->shouldReceive('send')->
      once()->
      with('/.*\/test/', 'DELETE', Mockery::type('array'), null)->
      andReturn($responseMock);
    $responseMock->shouldReceive('getBody->getContents')->andReturn('');

    $this->assertEquals(null, $this->resource->delete('test'));
  }

  public function testAdapter404Exception() {
    try {
      $this->adapterMock->shouldReceive('send')->
        once()->
        andThrow($this->getExceptionMock(404));

      $this->resource->get('test');
    }
    catch(\Exception $e) {
      $this->assertRegExp('/.*resource does not exist.*/', $e->getMessage());
    }
  }

  public function testAdapter4XXException() {
    try {
      $this->adapterMock->shouldReceive('send')->
        once()->
        andThrow($this->getExceptionMock(400));

      $this->resource->get('test');
    }
    catch(\Exception $e) {
      $this->assertRegExp('/Received bad response.*/', $e->getMessage());
    }
  }

  public function testAdapter5XXException() {
    try {
      $this->adapterMock->shouldReceive('send')->
        once()->
        andThrow(new \Exception('Something went wrong.'));

      $this->resource->get('test');
    }
    catch(\Exception $e) {
      $this->assertRegExp('/Unable to contact.*API.*/', $e->getMessage());
    }
  }

}
