<?php

namespace SparkPost;

use Mockery;
use Sparkpost\SuppressionHandler;

class SuppressionHandlertest extends \PHPUnit_Framework_TestCase
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
        $this->sut = new SuppressionHandler($this->sparkPostMock);
    }

    public function tearDown()
    {
        Mockery::close();
    }

    public function testSearch()
    {
        $testBody = ['results' => ['my' => 'test']];

        $responseMock = Mockery::mock();
        $this->sparkPostMock->httpAdapter->shouldReceive('send')->
            once()->
            with("/suppression-list/", 'GET', Mockery::type('array'), null)->
            andReturn($responseMock);
        $responseMock->shouldReceive('getStatusCode')->andReturn(200);
        $responseMock->shouldReceive('getBody->getContents')->andReturn(json_encode($testBody));
        $this->assertEquals($testBody, $this->sut->search([]));
    }

    public function testInsert()
    {
        $testBody = ['recipients' => [['email' => 'recp_1@example.com']]];

        $responseMock = Mockery::mock();
        $this->sparkPostMock->httpAdapter->shouldReceive('send')->
            once()->
            with("/suppression-list/", 'PUT', Mockery::type('array'), Mockery::type('string'))->
            andReturn($responseMock);
        $responseMock->shouldReceive('getStatusCode')->andReturn(200);
        $responseMock->shouldReceive('getBody->getContents')->andReturn(json_encode($testBody));
        $this->assertEquals($testBody, $this->sut->insert($testBody));
    }
}
