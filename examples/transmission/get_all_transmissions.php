<?php
namespace Examples\Transmisson;
require_once (dirname(__FILE__).'/../bootstrap.php');

use SparkPost\SparkPost;
use SparkPost\Transmission;
use GuzzleHttp\Client;
use Ivory\HttpAdapter\Guzzle6HttpAdapter;

$key = 'YOURAPIKEY';
$httpAdapter = new Guzzle6HttpAdapter(new Client());
SparkPost::setConfig($httpAdapter, ['key'=>$key]);

try {
	$results = Transmission::all();
	var_dump($results);
	echo 'Congrats you can use your SDK!';
} catch (\Exception $exception) {
	echo $exception->getMessage();
}
?>
