<?php
namespace Examples\Transmisson;
require_once (dirname(__FILE__).'/../bootstrap.php');
use SparkPost\SparkPost;
use SparkPost\Transmission;
use GuzzleHttp\Client;
use Ivory\HttpAdapter\Guzzle6HttpAdapter;

$key = 'YOUR API KEY';
$httpAdapter = new Guzzle6HttpAdapter(new Client());
$sparky = new SparkPost($httpAdapter, ['key'=>$key]);

try {

	$results = $sparky->transmission->send([
		"campaign"=>"my-campaign",
		"from"=>"From Envelope <from@sparkpostbox.com>",
		"html"=>"<p>Hello World! Your name is: {{name}}</p>",
		"text"=>"Hello World!",
		"subject"=>"Example Email: {{name}}",
		"recipientList"=>'Example List'
	]);

	echo 'Congrats you can use your SDK!';
} catch (\Exception $exception) {
	echo $exception->getMessage();
}
?>
