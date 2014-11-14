<?php
namespace Examples\Transmisson;
require_once (dirname(__FILE__).'/../bootstrap.php');
use SparkPost\SparkPost;
use SparkPost\Transmission;

$key = 'YOURAPIKEY'; 
SparkPost::setConfig(array('key'=>$key));

try {
	$results = Transmission::send(array(
		"from"=>"From Envelope <from@example.com>",
		"recipients"=>array(
			array(
			"address"=>array(
					"email"=>"john.doe@sample.com"
				)
			)
		),
		"template"=>"my-template"
	));
	echo 'Congrats you can use your SDK!';
} catch (\Exception $exception) {
	echo $exception->getMessage();
}
?>