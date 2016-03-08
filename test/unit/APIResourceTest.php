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
    $this->sparkPostMock = Mockery::mock('SparkPost\SparkPost', function($mock) {
      $mock->shouldReceive('getHttpHeaders')->andReturn([]);
    });
    $this->sparkPostMock->httpAdapter = Mockery::mock();
    $this->resource = new APIResource($this->sparkPostMock);
    self::$utils = new ClassUtils($this->resource);
    self::$utils->setProperty($this->resource, 'sparkpost', $this->sparkPostMock);
  }

  public function tearDown()
    {
        Mockery::close();
    }

  public function testConstructorSetsUpSparkPostObject() {
    $this->sparkPostMock->newProp = 'new value';
    $this->assertEquals($this->sparkPostMock, self::$utils->getProperty($this->resource, 'sparkpost'));
  }

  public function testCreate() {
    $testInput = ['test'=>'body'];
    $testBody = ['results'=>['my'=>'test']];
    $responseMock = Mockery::mock();
    $this->sparkPostMock->httpAdapter->shouldReceive('send')->
      once()->
      with(Mockery::type('string'), 'POST', Mockery::type('array'), json_encode($testInput))->
      andReturn($responseMock);
    $responseMock->shouldReceive('getStatusCode')->andReturn(200);
    $responseMock->shouldReceive('getBody->getContents')->andReturn(json_encode($testBody));

    $this->assertEquals($testBody, $this->resource->create($testInput));
  }

  public function testUpdate() {
    $testInput = ['test'=>'body'];
    $testBody = ['results'=>['my'=>'test']];
    $responseMock = Mockery::mock();
    $this->sparkPostMock->httpAdapter->shouldReceive('send')->
      once()->
      with('/.*\/test/', 'PUT', Mockery::type('array'), json_encode($testInput))->
      andReturn($responseMock);
    $responseMock->shouldReceive('getStatusCode')->andReturn(200);
    $responseMock->shouldReceive('getBody->getContents')->andReturn(json_encode($testBody));

    $this->assertEquals($testBody, $this->resource->update('test', $testInput));
  }

  public function testGet() {
    $testBody = ['results'=>['my'=>'test']];
    $responseMock = Mockery::mock();
    $this->sparkPostMock->httpAdapter->shouldReceive('send')->
      once()->
      with('/.*\/test/', 'GET', Mockery::type('array'), null)->
      andReturn($responseMock);
    $responseMock->shouldReceive('getStatusCode')->andReturn(200);
    $responseMock->shouldReceive('getBody->getContents')->andReturn(json_encode($testBody));

    $this->assertEquals($testBody, $this->resource->get('test'));
  }

  public function testDelete() {
    $responseMock = Mockery::mock();
    $this->sparkPostMock->httpAdapter->shouldReceive('send')->
      once()->
      with('/.*\/test/', 'DELETE', Mockery::type('array'), null)->
      andReturn($responseMock);
    $responseMock->shouldReceive('getStatusCode')->andReturn(200);
    $responseMock->shouldReceive('getBody->getContents')->andReturn('');

    $this->assertEquals(null, $this->resource->delete('test'));
  }

  public function testAdapter404Exception() {
    try {
      $responseMock = Mockery::mock();
      $this->sparkPostMock->httpAdapter->shouldReceive('send')->
        once()->
        andReturn($responseMock);
      $responseMock->shouldReceive('getStatusCode')->andReturn(404);

      $this->resource->get('test');
    }
    catch(\Exception $e) {
      $this->assertRegExp('/.*resource does not exist.*/', $e->getMessage());
    }
  }

  public function testAdapter4XXException() {
    try {
      $testBody = ['errors'=>['my'=>'test']];
      $responseMock = Mockery::mock();
      $this->sparkPostMock->httpAdapter->shouldReceive('send')->
        once()->
        andReturn($responseMock);
      $responseMock->shouldReceive('getStatusCode')->andReturn(400);
      $responseMock->shouldReceive('getBody')->andReturn(json_encode($testBody));

      $this->resource->get('test');
    }
    catch(\Exception $e) {
      $this->assertRegExp('/Received bad response.*/', $e->getMessage());
    }
  }

  public function testAdapter5XXException() {
    try {
      $this->sparkPostMock->httpAdapter->shouldReceive('send')->
        once()->
        andThrow(new \Exception('Something went wrong.'));

      $this->resource->get('test');
    }
    catch(\Exception $e) {
      $this->assertRegExp('/Unable to contact.*API.*/', $e->getMessage());
    }
  }

}
