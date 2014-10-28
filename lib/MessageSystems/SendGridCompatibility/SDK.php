<?php
namespace MessageSystems\SendGridCompatibility;

use MessageSystems\Transmission;
use MessageSystems\SendGridCompatibility\Email;
use MessageSystems\Configuration;

class SDK{
	private $sparkPost;
	
	public function __construct($username, $password, $options = null) {
		//username isn't used in our system
		$opts = ['key'=>$password];
		if (!is_null($options)) {
			$opts = array_merge($opts, $options);
		}
		Configuration::setConfig($opts);
	}
	
	public function send(Email $email) {
		$email->send();
	}
}
?>