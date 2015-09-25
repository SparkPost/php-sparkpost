<?php
namespace SparkPost\Test;

use SparkPost\Transmission;
use SparkPost\Test\TestUtils\ClassUtils;
use \Mockery;

class TransmissionTest extends \PHPUnit_Framework_TestCase {

  private static $utils;
  private $adapterMock;
  private $resource;

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
    $this->resource = new Transmission($this->adapterMock, ['key'=>'a key']);
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


  public function testSend() {
    $responseMock = Mockery::mock();
    $body = ['text'=>'awesomesauce', 'content'=>['subject'=>'awesomeness']];
    $responseBody = ["results"=>"yay"];
    $this->adapterMock->shouldReceive('send')->
    once()->
    with('/.*\/transmissions/', 'POST', Mockery::type('array'), Mockery::type('string'))->
    andReturn($responseMock);

    $responseMock->shouldReceive('getBody->getContents')->andReturn(json_encode($responseBody));

    $this->assertEquals($responseBody, $this->resource->send($body));
  }

  public function testAllWithFilter() {
    $responseMock = Mockery::mock();
    $responseBody = ["results"=>"yay"];
    $this->adapterMock->shouldReceive('send')->
    once()->
    with('/.*transmissions.*?campaign_id=campaign&template_id=template/', 'GET', Mockery::type('array'), null)->
    andReturn($responseMock);

    $responseMock->shouldReceive('getBody->getContents')->andReturn(json_encode($responseBody));

    $this->assertEquals($responseBody, $this->resource->all('campaign', 'template'));
  }

  public function testAllWithOutFilter() {
    $responseMock = Mockery::mock();
    $responseBody = ["results"=>"yay"];
    $this->adapterMock->shouldReceive('send')->
    once()->
    with('/.*\/transmissions/', 'GET', Mockery::type('array'), null)->
    andReturn($responseMock);

    $responseMock->shouldReceive('getBody->getContents')->andReturn(json_encode($responseBody));

    $this->assertEquals($responseBody, $this->resource->all());
  }

  public function testFind() {
    $responseMock = Mockery::mock();
    $responseBody = ["results"=>"yay"];
    $this->adapterMock->shouldReceive('send')->
    once()->
    with('/.*\/transmissions.*\/test/', 'GET', Mockery::type('array'), null)->
    andReturn($responseMock);

    $responseMock->shouldReceive('getBody->getContents')->andReturn(json_encode($responseBody));

    $this->assertEquals($responseBody, $this->resource->find('test'));
  }

}
?>
