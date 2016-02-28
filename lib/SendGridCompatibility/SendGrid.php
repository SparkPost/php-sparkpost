<?php
namespace SparkPost\SendGridCompatibility;

use SparkPost\SparkPost;

class SendGrid{
  private $sparky;

  public function __construct($username, $password, $options = null, $httpAdapter) {
    //username isn't used in our system
    $opts = array('key'=>$password);
    if (!is_null($options)) {
      $opts = array_merge($opts, $options);
    }

    $this->sparky = new SparkPost($httpAdapter, $opts);
  }

  public function send(Email $email) {
    $this->sparky->transmission->send($email->toSparkPostTransmission());
  }
}
?>
