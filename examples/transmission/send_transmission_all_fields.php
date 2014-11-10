<?php
namespace Examples\Transmisson;
require_once (dirname(__FILE__).'/../bootstrap.php');
use SparkPost\SparkPost;
use SparkPost\Transmission;

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
	  "CustomHeaders"=>[
	    "X-Custom-Header"=>"Sample Custom Header"
	  ],
	  "trackOpens"=>false,
	  "trackClicks"=>false,
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