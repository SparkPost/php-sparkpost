<?php
namespace SparkPost\Test;
use SparkPost\Transmission;
use SparkPost\Test\TestUtils\ClassUtils;
use \Mockery;

class TransmissionTest extends \PHPUnit_Framework_TestCase {

  private static $utils;
  private $sparkPostMock;
  private $resource;

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
    $this->resource = new Transmission($this->sparkPostMock);
    self::$utils = new ClassUtils($this->resource);
  }

  public function tearDown(){
    Mockery::close();
  }

  public function testSend() {
    $responseMock = Mockery::mock();
    $body = ['text'=>'awesomesauce', 'content'=>['subject'=>'awesomeness']];
    $responseBody = ['results'=>'yay'];

    $this->sparkPostMock->httpAdapter->shouldReceive('send')->
      once()->
      with('/.*\/transmissions/', 'POST', Mockery::type('array'), Mockery::type('string'))->
      andReturn($responseMock);
    $responseMock->shouldReceive('getStatusCode')->andReturn(200);

    $responseMock->shouldReceive('getBody->getContents')->andReturn(json_encode($responseBody));

    $this->assertEquals($responseBody, $this->resource->send($body));
  }

  public function testAllWithFilter() {
    $responseMock = Mockery::mock();
    $responseBody = ['results'=>'yay'];
    $this->sparkPostMock->httpAdapter->shouldReceive('send')->
      once()->
      with('/.*transmissions.*?campaign_id=campaign&template_id=template/', 'GET', Mockery::type('array'), null)->
      andReturn($responseMock);
    $responseMock->shouldReceive('getStatusCode')->andReturn(200);

    $responseMock->shouldReceive('getBody->getContents')->andReturn(json_encode($responseBody));

    $this->assertEquals($responseBody, $this->resource->all('campaign', 'template'));
  }

  public function testAllWithOutFilter() {
    $responseMock = Mockery::mock();
    $responseBody = ['results'=>'yay'];
    $this->sparkPostMock->httpAdapter->shouldReceive('send')->
      once()->
      with('/.*\/transmissions/', 'GET', Mockery::type('array'), null)->
      andReturn($responseMock);
    $responseMock->shouldReceive('getStatusCode')->andReturn(200);

    $responseMock->shouldReceive('getBody->getContents')->andReturn(json_encode($responseBody));

    $this->assertEquals($responseBody, $this->resource->all());
  }

  public function testFind() {
    $responseMock = Mockery::mock();
    $responseBody = ['results'=>'yay'];
    $this->sparkPostMock->httpAdapter->shouldReceive('send')->
      once()->
      with('/.*\/transmissions.*\/test/', 'GET', Mockery::type('array'), null)->
      andReturn($responseMock);
    $responseMock->shouldReceive('getStatusCode')->andReturn(200);

    $responseMock->shouldReceive('getBody->getContents')->andReturn(json_encode($responseBody));

    $this->assertEquals($responseBody, $this->resource->find('test'));
  }

}
?>
