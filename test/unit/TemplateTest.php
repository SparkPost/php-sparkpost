<?php
namespace SparkPost\Test;

use SparkPost\Template;
use SparkPost\Test\TestUtils\ClassUtils;
use Mockery;

class TemplateTest extends \PHPUnit_Framework_TestCase
{

  private static $utils;
    private $sparkPostMock;
    private $resource;

  /**
   * (non-PHPdoc)
   * @before
   * @see PHPUnit_Framework_TestCase::setUp()
   */
  public function setUp()
  {
      $this->sparkPostMock = Mockery::mock('SparkPost\SparkPost', function ($mock) {
      $mock->shouldReceive('getHttpHeaders')->andReturn([]);
    });
      $this->sparkPostMock->httpAdapter = Mockery::mock();
      $this->resource = new Template($this->sparkPostMock);
      self::$utils = new ClassUtils($this->resource);
  }

    public function tearDown()
    {
        Mockery::close();
    }

    public function testCreate()
    {
        $responseMock = Mockery::mock();
        $body = ['id' => 'awesomesauce', 'content' => ['subject' => 'awesomeness']];
        $responseBody = ['results' => 'yay'];

        $this->sparkPostMock->httpAdapter->shouldReceive('send')->
      once()->
      with('/.*\/templates/', 'POST', Mockery::type('array'), Mockery::type('string'))->
      andReturn($responseMock);
        $responseMock->shouldReceive('getStatusCode')->andReturn(200);

        $responseMock->shouldReceive('getBody->getContents')->andReturn(json_encode($responseBody));

        $this->assertEquals($responseBody, $this->resource->create($body));
    }

    public function testAll()
    {
        $responseMock = Mockery::mock();
        $responseBody = ['results' => 'yay'];
        $this->sparkPostMock->httpAdapter->shouldReceive('send')->
      once()->
      with('/.*\/templates/', 'GET', Mockery::type('array'), null)->
      andReturn($responseMock);
        $responseMock->shouldReceive('getStatusCode')->andReturn(200);

        $responseMock->shouldReceive('getBody->getContents')->andReturn(json_encode($responseBody));

        $this->assertEquals($responseBody, $this->resource->all());
    }

    public function testFind()
    {
        $responseMock = Mockery::mock();
        $responseBody = ['results' => 'yay'];
        $this->sparkPostMock->httpAdapter->shouldReceive('send')->
      once()->
      with('/.*\/templates.*\/test/', 'GET', Mockery::type('array'), null)->
      andReturn($responseMock);
        $responseMock->shouldReceive('getStatusCode')->andReturn(200);

        $responseMock->shouldReceive('getBody->getContents')->andReturn(json_encode($responseBody));

        $this->assertEquals($responseBody, $this->resource->find('test'));
    }

    public function testPreview()
    {
        $responseMock = Mockery::mock();
        $responseBody = ['results' => 'yay'];
        $substitution_data = ["a" => "b"];
        $requestBody = ['substitution_data' => $substitution_data];
        $this->sparkPostMock->httpAdapter->shouldReceive('send')->
      once()->
      with('/\/templates\/test\/preview\?draft=true/', 'POST', Mockery::type('array'), json_encode($requestBody))->
      andReturn($responseMock);
        $responseMock->shouldReceive('getStatusCode')->andReturn(200);

        $responseMock->shouldReceive('getBody->getContents')->andReturn(json_encode($responseBody));

        $this->assertEquals($responseBody, $this->resource->preview('test', false, $substitution_data));
    }
}
