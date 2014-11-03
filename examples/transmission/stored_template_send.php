<?php
namespace Examples\Transmisson;
require_once (dirname(__FILE__).'/../bootstrap.php');
use MessageSystems\SparkPost;
use MessageSystems\Transmission;

$key = 'YOURAPIKEY'; 
SparkPost::setConfig(['key'=>$key]);

try {
	$results = Transmission::send([
		"from"=>"From Envelope <from@example.com>",
		"recipients"=>[
			[
			"address"=>[
					"email"=>"john.doe@sample.com"
				]
			]
		],
		"template"=>"my-template"
	]);
	echo 'Congrats you can use your SDK!';
} catch (\Exception $exception) {
	echo $exception->getMessage();
}
?>