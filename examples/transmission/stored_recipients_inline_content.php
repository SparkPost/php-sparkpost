<?php
namespace Examples\Transmisson;
require_once (dirname(__FILE__).'/../bootstrap.php');
use SparkPost\SparkPost;
use SparkPost\Transmission;

$key = 'YOURAPIKEY';
SparkPost::setConfig(['key'=>$key]);

try {

	$results = Transmission::send([
		"campaign"=>"my-campaign",
		"from"=>"From Envelope <sandbox@sparkpostbox.com>",
		"html"=>"<p>Hello World! Your name is: {{name}}</p>",
		"text"=>"Hello World!",
		"subject"=>"Example Email: {{name}}",
		"recipientList"=>'Example List'
	]);

	echo 'Congrats you can use your SDK!';
} catch (\Exception $exception) {
	echo $exception->getMessage();
}
?>
