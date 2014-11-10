<?php
namespace Examples\Transmisson;
require_once (dirname(__FILE__).'/../bootstrap.php');
use SparkPost\SparkPost;
use SparkPost\Transmission;

$key = 'YOURAPIKEY'; 
SparkPost::setConfig(['key'=>$key]);

try {
	$results = Transmission::find('Your Transmission Id');
	echo 'Congrats you can use your SDK!';
} catch (\Exception $exception) {
	echo $exception->getMessage();
}
?>