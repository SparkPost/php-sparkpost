<?php
namespace Examples\Unwrapped;
require_once (dirname(__FILE__).'/../bootstrap.php');
use SparkPost\SparkPost;
use SparkPost\APIResource;

$key = 'YOURAPIKEY'; 
SparkPost::setConfig(array('key'=>$key));

try {
	// define the endpoint
	APIResource::$endpoint = 'templates';
	
	$templateConfig = array(
		'name' => 'Summer Sale!',
		'content.from' => 'marketing@bounces.company.example',
		'content.subject' => 'Summer deals',
		'content.html' => '<b>Check out these deals!</b>',
	);
	$results = APIResource::sendRequest($templateConfig);
	echo 'Congrats you can use your SDK!';
} catch (\Exception $exception) {
	echo $exception->getMessage();
}
?>