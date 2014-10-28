<?php
namespace Examples\Transmisson;
	require_once (dirname(__FILE__).'/../bootstrap.php');
	
	use SparkPost\SparkPost;
	
	$key = 'YOURAPIKEY';
	$sdk = new SparkPost(['key'=>$key]);
	
	$transmission = $sdk->Transmission();
	
	try {
		$results = $transmission->all();
		echo 'Congrats you can use your SDK!';
	} catch (\Exception $exception) {
		echo $exception->getMessage();
	}
?>