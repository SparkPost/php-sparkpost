<?php
namespace Examples\Transmisson;
require_once (dirname(__FILE__).'/../bootstrap.php');
use MessageSystems\SparkPost;
use MessageSystems\Transmission;

$key = 'YOURAPIKEY';
SparkPost::setConfig(['key'=>$key]);

try{
	$results = Transmission::send([
	  "campaign"=>"my-campaign",
	  "metadata"=>[
	   "sample_campaign"=>true,
	   "type"=>"these are custom fields"
	  ],
	  "substitutionData"=>[
	    "name"=>"Test Name"
	  ],
	  "description"=>"my description",
	  "replyTo"=>"reply@test.com",
	  "headers"=>[
	    "X-Custom-Header"=>"Sample Custom Header"
	  ],
	  "openTracking"=>false,
	  "clickTracking"=>false,
	  "from"=>"From Envelope <from@example.com>",
	  "html"=>"<p>Hello World! Your name is: {{name}}</p>",
	  "text"=>"Hello World!",
	  "subject"=>"Example Email: {{name}}",
	  "recipients"=>[
	    [
	      "address"=>[
	        "email"=>"john.doe@sample.com"
	      ]
	    ]
	  ]
	]);
	echo 'Congrats you can use your SDK!';
} catch (\Exception $exception) {
	echo $exception->getMessage();
}
?>