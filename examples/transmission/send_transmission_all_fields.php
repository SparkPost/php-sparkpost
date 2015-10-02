<?php
namespace Examples\Transmisson;
require_once (dirname(__FILE__).'/../bootstrap.php');

//pull in API key config
$configFile = file_get_contents(dirname(__FILE__) . "/../example-config.json");
$config = json_decode($configFile, true);

use SparkPost\SparkPost;
use SparkPost\Transmission;

SparkPost::setConfig(array('key'=>$config['api-key']));

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
