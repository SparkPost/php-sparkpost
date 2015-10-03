<?php
use SparkPost\SendGridCompatibility\Email;

class SendGridCompatibilityEmailTest extends \PHPUnit_Framework_TestCase {

  private $email;

  public function setup() {
    $this->email = new Email();
  }

  public function testConstruct() {
    $email = new Email();

    $this->assertInstanceOf('SparkPost\SendGridCompatibility\Email', $email);
    $this->assertInternalType('array', $email->model);
  }

  public function testAddTo() {
    $fakeEmail = 'joe.schmoe@test.com';
    $this->email->addTo($fakeEmail);

    $this->assertEquals(array(array('address'=>array('email'=>$fakeEmail))), $this->email->model['recipients']);
  }

  public function testAddToWithName() {
    $fakeEmail = 'joe.schmoe@test.com';
    $fakeName = 'Joe Schmoe';
    $this->email->addTo($fakeEmail, $fakeName);

    $this->assertEquals(array(array('address'=>array('email'=>$fakeEmail, 'name'=>$fakeName))), $this->email->model['recipients']);
  }

  public function testSetTos() {
    $tos = array();
    array_push($tos, array('address'=>array('email'=>'joe.schmoe@test.com', 'name'=>'Joe Schmoe')));
    array_push($tos, array('address'=>array('email'=>'jill.schmoe@test.com', 'name'=>'Jill Schmoe')));
    $this->email->setTos($tos);

    $this->assertEquals($tos, $this->email->model['recipients']);
  }

  public function testSetFrom() {
    $this->email->setFrom('test@email.com');

    $this->assertEquals(array('email'=>'test@email.com'), $this->email->model['from']);
  }


  public function testSetFromName() {
    $this->email->setFrom('test@email.com');
    $this->email->setFromName('Test Bot');

    $this->assertEquals(array('email'=>'test@email.com', 'name'=>'Test Bot'), $this->email->model['from']);
  }

  /**
   * @desc Tests that setting the fromName prior to setting the From field throws an exception
   * @expectedException Exception
   * @expectedExceptionMessage Must set 'From' prior to setting 'From Name'.
   */
  public function testSetFromNameWithoutAddress() {
    $this->email->setFromName('Test Bot');
  }

  public function testSetReplyto() {
    $this->email->setReplyTo('test@email.com');

    $this->assertEquals('test@email.com', $this->email->model['replyTo']);
  }
  /**
   * @expectedException Exception
   * @expectedExceptionMessage Adding bcc recipients is not yet supported, try adding them as a 'to' address
   */
  public function testAddBcc() {
    $this->email->addBcc('test@email.com');
  }

  public function testSetSubject() {
    $this->email->setSubject('Awesome Subject');

    $this->assertEquals('Awesome Subject', $this->email->model['subject']);
  }

  public function testSetText() {
    $value = 'This is some plain/text';
    $this->email->setText($value);

    $this->assertEquals($value, $this->email->model['text']);
  }

  public function testSetHtml() {
    $value = '<html><body><p>This is some html</p></body></html>';
    $this->email->setHtml($value);

    $this->assertEquals($value, $this->email->model['html']);
  }

  /**
   * @desc test that adding a category throws an exception since we don't support tags at transmission level yet
   * @expectedException Exception
   * @expectedExceptionMessage Adding categories is not yet supported
   */
  public function testAddCategory() {
    $this->email->addCategory('');
  }

  /**
   * @desc Tests that setting an attachment throws a meaningful exception
   * @expectedException Exception
   * @expectedExceptionMessage Adding attachments is not yet supported
   */
  public function testAddAttachment() {
    $this->email->addAttachment('blah');
  }

  public function testAddSubstitution() {
    $this->email->addSubstitution('item', 'baseball bat');

    $this->assertEquals(array('item'=>'baseball bat'), $this->email->model['substitutionData']);
  }

  public function testAddSection() {
    $this->email->addSection('item', 'baseball bat');

    $this->assertEquals(array('item'=>'baseball bat'), $this->email->model['substitutionData']);
  }

  /**
   * @desc Tests that setting an attachment throws a meaningful exception
   * @expectedException Exception
   * @expectedExceptionMessage Adding Unique Arguments is not yet supported
   */
  public function testAddUniqueArguement() {
    $this->email->addUniqueArg('blah', 'someblah');
  }


  /**
   * @desc Tests that setting an unique argument throws a meaningful exception
   * @expectedException Exception
   * @expectedExceptionMessage Setting Unique Arguments is not yet supported
   */
  public function testSetUniqueArgs() {
    $this->email->setUniqueArgs(array('blah', 'andBlah'));
  }


  public function testAddHeader() {
    $value = 'My Header';
    $this->email->addHeader('X-header', $value);

    $this->assertEquals(array('X-header'=>$value), $this->email->model['customHeaders']);
  }

  public function testToSparkPostTransmission() {
    $this->assertInternalType('array', $this->email->toSparkPostTransmission());
  }
}

?>
