<?php
namespace Examples\Transmisson;
require_once (dirname(__FILE__).'/../bootstrap.php');
use SparkPost\SparkPost;

$key = 'YOURAPIKEY';
$sdk = new SparkPost(['key'=>$key]);



$transmission = $sdk->Transmission();

$transmission->setCampaign('my campaign')
	->setMetadata([
		"sample_campaign"=>"true",
		"type"=>"test type meta data"
	])
	->setSubstitutionData([
		"name"=>"Test Name"
	])
	->setDescription('My Description')
	->setReturnPath('return@example.com')
	->setReplyTo('reply@test.com')
	->setContentHeaders([
		"X-Custom-Header"=>"Sample Custom Header"
	])
	->disableOpenTracking()
	->disableClickTracking()
	->setFrom('From Envelope <from@example.com>')
	->addRecipient([
		"address"=> [
			"email"=>"john.doe@sample.com"
		]
	])
	->setSubject('Example Email: {{name}}')
	->setHTMLContent('<p>Hello World! Your name is: {{name}}</p>')
	->setTextContent('Hello World!');


try {
	$results = $transmission->send();
	echo 'Congrats you can use your SDK!';
} catch (\Exception $exception) {
	echo $exception->getMessage();
}