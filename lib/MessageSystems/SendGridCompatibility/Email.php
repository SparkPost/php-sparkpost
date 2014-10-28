<?php
namespace MessageSystems\SendGridCompatibility;

use MessageSystems\Transmission;

class Email {
	private $transmission;
	
	public function __construct() {
		$this->transmission = new Transmission();
	}
	
	public function send() {
		$this->transmission->send();
	}
	
	public function addTo($address) {
		$this->transmission->addRecipient($address);
		return $this;
	}
	
	public function setTos(Array $addresses) {
		$this->transmission->addRecipients($addresses);
		return $this;
	}
	
	/**
	 * 
	 * @param string $address
	 * @return \MessageSystems\SendGridCompatibility\Email
	 */
	public function setFrom($address) {
		$this->transmission->setFrom($address);
		return $this;
	}
	
	/**
	 * TODO:figure this out 
	 * @param string $name
	 */
	public function setFromName($name) {
		
		return $this;
	}
	
	
	/**
	 * 
	 * @param string $address
	 * @return \MessageSystems\SendGridCompatibility\Email
	 */
	public function setReplyTo($address) {
		$this->transmission->setReplyTo($address);
		return $this;
	}
	
	/**
	 * TODO: Does this work?
	 * 
	 * 
	 * @param string $address
	 * @return \MessageSystems\SendGridCompatibility\Email
	 */
	public function addBcc($address) {
		$this->transmission->addRecipient($address);
		return $this;
	}
	
	public function setSubject($subject) {
		$this->transmission->setSubject($subject);
		return $this;
	}
	
	public function setText($text) {
		$this->transmission->setTextContent($text);
		return $this;
	}
	
	public function setHtml($html) {
		$this->transmission->setHTMLContent($html);
		return $this;
	}
}
?>