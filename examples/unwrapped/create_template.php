<?php
namespace Examples\Unwrapped;
require_once (dirname(__FILE__).'/../bootstrap.php');
use SparkPost\SparkPost;
use SparkPost\APIResource;
use GuzzleHttp\Client;
use Ivory\HttpAdapter\Guzzle6HttpAdapter;

$key = 'YOUR API KEY';
$httpAdapter = new Guzzle6HttpAdapter(new Client());
SparkPost::configure($httpAdapter, ['key'=>$key]);

try {
	// define the endpoint
	APIResource::$endpoint = 'templates';

	$templateConfig = [
		'name' => 'Summer Sale!',
    'id'=>'summer-sale',
		'content'=> [
      'from' => 'john.doe@sparkpostbox.com',
		  'subject' => 'Summer deals',
		  'html' => '<b>Check out these deals!</b>'
    ]
	];
	$results = APIResource::create($templateConfig);
  var_dump($results);
	echo 'Congrats you can use your SDK!';
} catch (\Exception $exception) {
	echo $exception->getMessage();
}
?>
