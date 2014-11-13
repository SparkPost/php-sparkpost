<?php
namespace SparkPost\SendGridCompatibility;

use SparkPost\Transmission;
use SparkPost\SendGridCompatibility\Email;
use SparkPost\Configuration;

class SendGrid{
	public function __construct($username, $password, $options = null) {
		//username isn't used in our system
		$opts = array('key'=>$password);
		if (!is_null($options)) {
			$opts = array_merge($opts, $options);
		}
		Configuration::setConfig($opts);
	}
	
	public function send(Email $email) {
		Trasmission::send($email->toSparkPostTransmission());
	}
}
?>