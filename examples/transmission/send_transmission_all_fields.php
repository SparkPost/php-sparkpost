<?php
namespace Examples\Transmisson;
require_once (dirname(__FILE__).'/../bootstrap.php');
use SparkPost\SparkPost;
use SparkPost\Transmission;

$key = 'YOURAPIKEY';
SparkPost::setConfig(array('key'=>$key));

try{
	$results = Transmission::send(array(
	  "campaign"=>"my-campaign",
	  "metadata"=>array(
	   "sample_campaign"=>true,
	   "type"=>"these are custom fields"
	  ),
	  "substitutionData"=>array(
	    "name"=>"Test Name"
	  ),
	  "description"=>"my description",
	  "replyTo"=>"reply@test.com",
	  "customHeaders"=>array(
	    "X-Custom-Header"=>"Sample Custom Header"
	  ),
	  "trackOpens"=>false,
	  "trackClicks"=>false,
	  "from"=>"From Envelope <from@example.com>",
	  "html"=>"<p>Hello World! Your name is: {{name}}</p>",
	  "text"=>"Hello World!",
	  "subject"=>"Example Email: {{name}}",
	  "recipients"=>array(
	    array(
	      "address"=>array(
	        "email"=>"john.doe@sample.com"
	      )
	    )
	  )
	));
	echo 'Congrats you can use your SDK!';
} catch (\Exception $exception) {
	echo $exception->getMessage();
}
?>