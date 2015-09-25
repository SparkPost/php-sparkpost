<?php
namespace Examples\Unwrapped;
require_once (dirname(__FILE__).'/../bootstrap.php');

//pull in API key config
$configFile = file_get_contents(dirname(__FILE__) . "/../example-config.json");
$config = json_decode($configFile, true);

use SparkPost\SparkPost;
use SparkPost\APIResource;

SparkPost::setConfig(array('key'=>$config['api-key']));

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
