<?php
namespace SparkPost\Test;
use SparkPost\APIResponseException;

class APIResourceExceptionTest extends \PHPUnit_Framework_TestCase {

  private $message;
  private $code;
  private $description;
  private $exception;

  /**
   * (non-PHPdoc)
   * @before
   * @see PHPUnit_Framework_TestCase::setUp()
   */
  public function setUp() {
    $this->message = 'Test message';
    $this->code = 400;
    $this->description = 'Test description';
    $this->exception = new APIResponseException(NULL, 0, $this->message, $this->code, $this->description);
  }

  public function testAPIMessage() {
    $this->assertEquals($this->message, $this->exception->getAPIMessage());
  }

  public function testAPICode() {
    $this->assertEquals($this->code, $this->exception->getAPICode());
  }

  public function testAPIDescription() {
    $this->assertEquals($this->description, $this->exception->getAPIDescription());
  }

}
