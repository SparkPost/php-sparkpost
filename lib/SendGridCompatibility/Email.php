<?php
namespace SparkPost\SendGridCompatibility;

class Email {
	public $model;
	
	public function __construct() {
		$this->model = array();
	}
	
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
	
	public function setTos(array $addresses) {
		$this->model['recipients'] = $addresses;
		return $this;
	}
	
	/**
	 * 
	 * @param string $address
	 * @return \MessageSystems\SendGridCompatibility\Email
	 */
	public function setFrom($address) {
		$this->model['from'] = array('email' => $address);
		return $this;
	}
	
	/**
	 * @param string $name
	 */
	public function setFromName($name) {
		if(!isset($this->model['from'])){
			throw new \Exception('Must set "From" prior to setting "From Name".');
		}
		$this->model['from']['name'] = $name;
		return $this;
	}
	
	/**
	 * 
	 * @param string $address
	 * @return \MessageSystems\SendGridCompatibility\Email
	 */
	public function setReplyTo ($address) {
		$this->model['replyTo'] = $address;
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
		if (!isset($this->model['bcc'])) {
			$this->model['bcc'] = array();
		}
		array_push($this->model['bcc'], $address);
		return $this;
	}
	
	public function setSubject($subject) {
		$this->model['subject'] = $subject;
		return $this;
	}
	
	public function setText($text) {
		$this->model['text'] = $text;
		return $this;
	}
	
	public function setHtml($html) {
		$this->model['html'] = $html;
		return $this;
	}
	
	public function addCategory($category) {
		if (!isset($this->model['tags'])) {
			$this->model['tags'] = array();
		}
		array_push($this->model['tags'], $category);
		return $this;
	}
	
	/**
	 * 
	 * @throws Exception
	 * @param mixed $attachment
	 */
	public function addAttachment($attachment) {
		throw new \Exception('Adding attachments is not yet supported');
	}
	
	/**
	 * @desc Sets the name attribute on the most recent set email address
	 * @param string $name
	 */
	public function addSubstitution($name, $values) {
		if (!isset($this->model['substitutionData'])) {
			$this->model['substitutionData'] = array();
		}
		$this->model['substitutionData'][$name] = $values;
		
		return $this;
	}
	
	public function addSection($name, $values) {
		$this->addSubstitution($name, $values);
	}
	
	/**
	 *
	 * @throws Exception
	 * @param mixed $attachment
	 */
	public function addUniqueArg($key, $value) {
		throw new \Exception('Adding Unique Arguments is not yet supported');
	}
	
	/**
	 *
	 * @throws Exception
	 * @param mixed $attachment
	 */
	public function setUniqueArgs(array $values) {
		throw new \Exception('Setting Unique Arguments is not yet supported');
	}
	
	
	public function addHeader($name, $value) {
		if (!isset($this->model['customHeaders'])) {
			$this->model['customHeaders'] = array();
		}
		$this->model['customHeaders'][$name] = $value;
	}
	
	public function toMsysTransmission() {
		return $this->model;
	}
}
?>