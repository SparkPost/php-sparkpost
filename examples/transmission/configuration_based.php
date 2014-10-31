<?php
namespace Examples\Transmisson;	
	require_once (dirname(__FILE__).'/../bootstrap.php');

	use MessageSystems\SparkPost;
	use MessageSystems\Transmission;

	$key = 'YOURAPIKEY'; 
	SparkPost::setConfig(['key'=>$key]);
	
	try {
		$results = Transmission::send([
			"returnPath"=>"return@example.com",
			"from"=>"From Envelope <from@example.com>",
	  		"html"=>"<p>Hello World!</p>",
	  		"text"=>"Hello World!",
	  		"subject"=>"Example Email",
	  		"recipients"=>[
	    		[
	    			"address"=>[
	        			"email"=>"jordan.nornhold@rackspace.messagesystems.com"
	    			]
	      		]
	    	]
		]);
		echo 'Congrats you can use your SDK!';
		
		var_dump(Transmission::$structure);
	} catch (\Exception $exception) {
		echo $exception->getMessage();
	}
?>