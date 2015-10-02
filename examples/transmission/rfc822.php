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
		'recipients'=>array(
			array(
			    'address'=>array(
			    	'email'=>'john.doe@sample.com'
			 	)
			)
		),
		'rfc822'=>"Content-Type: text/plain\nFrom: From Envelope <from@example.com>\nSubject: Example Email\n\nHello World"
	));
	echo 'Congrats you can use your SDK!';
} catch (\Exception $exception) {
	echo $exception->getMessage();
}
?>
