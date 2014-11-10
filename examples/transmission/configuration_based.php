<?php
namespace Examples\Transmisson;	
require_once (dirname(__FILE__).'/../bootstrap.php');

use SparkPost\SparkPost;
use SparkPost\Transmission;

$key = 'YOURAPIKEY'; 
SparkPost::setConfig(['key'=>$key]);

try {
	$results = Transmission::send([
		"from"=>"From Envelope <from@example.com>",
  		"html"=>"<p>Hello World!</p>",
  		"text"=>"Hello World!",
  		"subject"=>"Example Email",
  		"recipients"=>[
    		[
    			"address"=>[
        			"email"=>"john.doe@example.com"
    			]
      		]
    	]
	]);
	echo 'Congrats you can use your SDK!';
} catch (\Exception $exception) {
	echo $exception->getMessage();
}
?>