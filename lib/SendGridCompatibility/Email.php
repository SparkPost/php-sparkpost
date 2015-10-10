<?php
namespace SparkPost\SendGridCompatibility;

class Email {
  public $model;


  /**
   * @desc Sets up the model for saving the configuration
   */
  public function __construct() {
    $this->model = array();
  }

  /**
   * @desc adds addresses as recipients
   * @param string $address
   * @param string $name optional
   * @return \SparkPost\SendGridCompatibility\Email
   */
   public function addTo($address, $name = null) {
    if (!isset($this->model['recipients'])) {
      $this->model['recipients'] = array();
    }

    if(isset($name)) {
      $address = array('address'=>array('email'=>$address, 'name'=>$name));
    } else {
      $address = array('address'=>array('email'=>$address));
    }

    array_push($this->model['recipients'], $address);
    return $this;
  }

  /**
  * @desc explicitly sets a list of addresses
  * @param array $addresses
  * @return \SparkPost\SendGridCompatibility\Email
  */
  public function setTos(array $addresses) {
    $this->model['recipients'] = $addresses;
    return $this;
  }

  /**
   * @desc sets the from address
   * @param string $address
   * @return \MessageSystems\SendGridCompatibility\Email
   */
  public function setFrom($address) {
    $this->model['from'] = array('email' => $address);
    return $this;
  }

  /**
   * @desc sets the name for the from address
   * @param string $name
   */
  public function setFromName($name) {
    if(!isset($this->model['from'])){
      throw new \Exception('Must set \'From\' prior to setting \'From Name\'.');
    }
    $this->model['from']['name'] = $name;
    return $this;
  }

  /**
   * @desc sets the reply to field
   * @param string $address
   * @return \MessageSystems\SendGridCompatibility\Email
   */
  public function setReplyTo ($address) {
    $this->model['replyTo'] = $address;
    return $this;
  }

  /**
   * @desc throws an error because bcc fields are not yet implemented.
   * @throws \Exception
   * @param string $address
   * @return \MessageSystems\SendGridCompatibility\Email
   */
  public function addBcc($address) {
    throw new \Exception('Adding bcc recipients is not yet supported, try adding them as a \'to\' address');
  }

  /**
   * @desc sets the subject header
   * @param string $subject
   * @return \SparkPost\SendGridCompatibility\Email
   */
  public function setSubject($subject) {
    $this->model['subject'] = $subject;
    return $this;
  }

  /**
   * @desc sets the text body
   * @param string $text
   * @return \SparkPost\SendGridCompatibility\Email
   */
  public function setText($text) {
    $this->model['text'] = $text;
    return $this;
  }

  /**
   * @desc sets the html body
   * @param string $html
   * @return \SparkPost\SendGridCompatibility\Email
   */
  public function setHtml($html) {
    $this->model['html'] = $html;
    return $this;
  }

  /**
   * @desc Throws an exception since adding categories is not yet supported
   * @throws \Exception
   * @param string $category
   * @throws \Exception
   */
  public function addCategory($category) {
    throw new \Exception('Adding categories is not yet supported');
  }

  /**
   * @desc Throws an exception since adding attachments is not yet supported
   * @throws Exception
   * @param mixed $attachment
   */
  public function addAttachment($attachment) {
    throw new \Exception('Adding attachments is not yet supported');
  }

  /**
   * @desc Adds transmission level substitution data
   * @param string $name
   * @param mixed $values
   * @return \SparkPost\SendGridCompatibility\Email
   */
  public function addSubstitution($name, $values) {
    if (!isset($this->model['substitutionData'])) {
      $this->model['substitutionData'] = array();
    }
    $this->model['substitutionData'][$name] = $values;

    return $this;
  }

  /**
   * @desc Adds transmission level substitution data
   * @param string $name
   * @param mixed $values
   */
  public function addSection($name, $values) {
    $this->addSubstitution($name, $values);
  }

  /**
   * @desc Throws an exception because arguments for third party systems is not supported
   * @throws Exception
   * @param mixed $value
   */
  public function addUniqueArg($key, $value) {
    throw new \Exception('Adding Unique Arguments is not yet supported');
  }

  /**
   * @desc Throws an exception because arguments for third party systems is not supported
   * @throws Exception
   * @param mixed $values
   */
  public function setUniqueArgs(array $values) {
    throw new \Exception('Setting Unique Arguments is not yet supported');
  }

  /**
   * @desc Adds custom headers to the email header
   * @param string $name
   * @param string $value
   */
  public function addHeader($name, $value) {
    if (!isset($this->model['customHeaders'])) {
      $this->model['customHeaders'] = array();
    }
    $this->model['customHeaders'][$name] = $value;
  }

  /**
   * @desc converts this object to a configuration for a SparkPost transmission
   * @return array
   */
  public function toSparkPostTransmission() {
    return $this->model;
  }
}
?>
