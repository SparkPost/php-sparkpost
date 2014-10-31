<?php
namespace Examples\Transmisson;
require_once (dirname(__FILE__).'/../bootstrap.php');
	use MessageSystems\SparkPost;
	use MessageSystems\Transmission;
	
	$key = 'YOURAPIKEY'; 
	SparkPost::setConfig(['key'=>$key]);

try {
	$results = Transmission::send([
		'recipients'=>[
			[
			    'address'=>[
			    	'email'=>'john.doe@sample.com'
			 	]
			]
		],
		'rfc822Part'=>"Content-Type: text/plain\nFrom: From Envelope <from@example.com>\nSubject: Example Email\n\nHello World"
	]);
	echo 'Congrats you can use your SDK!';
} catch (\Exception $exception) {
	echo $exception->getMessage();
}
?>