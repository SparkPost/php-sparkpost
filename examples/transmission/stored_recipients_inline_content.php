<?php
namespace Examples\Transmisson;
require_once (dirname(__FILE__).'/../bootstrap.php');

//pull in API key config
$configFile = file_get_contents(dirname(__FILE__) . "/../example-config.json");
$config = json_decode($configFile, true);

use SparkPost\SparkPost;
use SparkPost\Transmission;

SparkPost::setConfig(array('key'=>$config['api-key']));

try {

	$results = Transmission::send(array(
		"campaign"=>"my-campaign",
		"from"=>"From Envelope <from@example.com>",
		"html"=>"<p>Hello World! Your name is: {{name}}</p>",
		"text"=>"Hello World!",
		"subject"=>"Example Email: {{name}}",
		"recipientList"=>'Example List'
	));

	echo 'Congrats you can use your SDK!';
} catch (\Exception $exception) {
	echo $exception->getMessage();
}
?>
