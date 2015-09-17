<?php
namespace Examples\Transmisson;
require_once (dirname(__FILE__).'/../bootstrap.php');
use SparkPost\SparkPost;
use SparkPost\Transmission;
use GuzzleHttp\Client;
use Ivory\HttpAdapter\Guzzle6HttpAdapter;

$key = 'YOUR API KEY';
$httpAdapter = new Guzzle6HttpAdapter(new Client());
SparkPost::configure($httpAdapter, ['key'=>$key]);

try {
	$results = Transmission::send([
		'recipients'=>[
			[
			  'address'=>[
			    'email'=>'john.doe@example.com'
			  ]
			]
		],
		'rfc822'=>"Content-Type: text/plain\nFrom: From Envelope <from@sparkpostbox.com>\nSubject: Example Email\n\nHello World"
	]);
	echo 'Congrats you can use your SDK!';
} catch (\Exception $exception) {
	echo $exception->getMessage();
}
?>
