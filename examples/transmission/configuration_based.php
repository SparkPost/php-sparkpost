<?php
namespace Examples\Transmisson;	
	require_once (dirname(__FILE__).'/../bootstrap.php');

	use SparkPost\SparkPost;

	$key = 'YOURAPIKEY'; 
	$sdk = new SparkPost(['key'=>$key]);
	
	$transmission = $sdk->Transmission([
		"return_path"=>"return@example.com",
		"from"=>"From Envelope <from@example.com>",
  		"html"=>"<p>Hello World!</p>",
  		"text"=>"Hello World!",
  		"subject"=>"Example Email",
  		"recipients"=>[
    		[
    			"address"=>[
        			"email"=>"john.doe@sample.com"
    			]
      		]
    	]		
	]);
	
	try {
		$results = $transmission->send();
		echo 'Congrats you can use your SDK!';
	} catch (\Exception $exception) {
		echo $exception->getMessage();
	}
?>