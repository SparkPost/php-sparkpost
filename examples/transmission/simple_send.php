<?php
namespace Examples\Transmisson;
require_once (dirname(__FILE__).'/../bootstrap.php');

use SparkPost\SparkPost;
use SparkPost\Transmission;

$key = 'YOURAPIKEY';
SparkPost::setConfig(['key'=>$key]);

try {
	$results = Transmission::send([
		"from"=>"From Envelope <sandbox@sparkpostbox.com>",
  		"html"=>"<p>Hello World!</p>",
  		"text"=>"Hello World!",
  		"subject"=>"Example Email",
  		"recipients"=>[
    		[
    			"address"=>[
        		"email"=>"nornholdj@gmail.com"
    			]
      	]
    	]
	]);
	echo 'Congrats you can use your SDK!';
} catch (\Exception $exception) {
	echo 'There has been an issue';
	var_dump($exception);
	echo $exception->getMessage();
}
?>
