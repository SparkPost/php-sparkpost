<?php

namespace SparkPost;

use Mockery;

class MessageEventTest extends \PHPUnit_Framework_TestCase
{
    private $sparkPostMock;
    private $sut;

  /**
   * (non-PHPdoc).
   *
   * @before
   *
   * @see PHPUnit_Framework_TestCase::setUp()
   */
  public function setUp()
  {
      $this->sparkPostMock = Mockery::mock('SparkPost\SparkPost', function ($mock) {
      $mock->shouldReceive('getHttpHeaders')->andReturn([]);
    });
      $this->sparkPostMock->httpAdapter = Mockery::mock();
      $this->sut = new MessageEvents($this->sparkPostMock);
  }

    public function testDateTimeConversion()
    {
        $testBody = ['results' => ['my' => 'test']];
        $testFrom = new \DateTime('1978-08-27 04:05:02');
        $testFromStr = urlencode('1978-08-27T04:05');
        $testTo = new \DateTime('2016-04-04 19:00');
        $testToStr = urlencode('2016-04-04T19:00');

        $responseMock = Mockery::mock();
        $this->sparkPostMock->httpAdapter->shouldReceive('send')->
      once()->
      with("/message-events/?from={$testFromStr}&to={$testToStr}", 'GET', Mockery::type('array'), null)->
      andReturn($responseMock);
        $responseMock->shouldReceive('getStatusCode')->andReturn(200);
        $responseMock->shouldReceive('getBody->getContents')->andReturn(json_encode($testBody));

        $this->assertEquals($testBody, $this->sut->search(['from' => $testFrom, 'to' => $testTo]));
    }

    public function testDocumentation()
    {
        $testBody = ['results' => ['my' => 'test']];
        $responseMock = Mockery::mock();
        $this->sparkPostMock->httpAdapter->shouldReceive('send')->
      once()->
      with('/message-events/events/documentation', 'GET', Mockery::type('array'), null)->
      andReturn($responseMock);
        $responseMock->shouldReceive('getStatusCode')->andReturn(200);
        $responseMock->shouldReceive('getBody->getContents')->andReturn(json_encode($testBody));

        $this->assertEquals($testBody, $this->sut->documentation());
    }

    public function testSamples()
    {
        $testBody = ['results' => ['my' => 'test']];
        $responseMock = Mockery::mock();
        $this->sparkPostMock->httpAdapter->shouldReceive('send')->
      once()->
      with('/message-events/events/samples?events='.urlencode('delivery,bounce'), 'GET', Mockery::type('array'), null)->
      andReturn($responseMock);
        $responseMock->shouldReceive('getStatusCode')->andReturn(200);
        $responseMock->shouldReceive('getBody->getContents')->andReturn(json_encode($testBody));

        $this->assertEquals($testBody, $this->sut->samples(['delivery', 'bounce']));
    }
}
