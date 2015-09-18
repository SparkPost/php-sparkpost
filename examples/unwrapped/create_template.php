<?php
namespace Examples\Unwrapped;
require_once (dirname(__FILE__).'/../bootstrap.php');
use GuzzleHttp\Client;
use Ivory\HttpAdapter\Guzzle6HttpAdapter;

$key = 'YOUR API KEY';
$httpAdapter = new Guzzle6HttpAdapter(new Client());
$resource = new \SparkPost\APIResource($httpAdapter, ['key'=>$key]);

try {
	// define the endpoint
	$resource->endpoint = 'templates';

	$templateConfig = [
		'name' => 'Summer Sale!',
    'id'=>'summer-sale',
		'content'=> [
      'from' => 'from@sparkpostbox.com',
		  'subject' => 'Summer deals',
		  'html' => '<b>Check out these deals!</b>'
    ]
	];
	$results = $resource->create($templateConfig);
	echo 'Congrats you can use your SDK!';
} catch (\Exception $exception) {
	echo $exception->getMessage();
}
?>
