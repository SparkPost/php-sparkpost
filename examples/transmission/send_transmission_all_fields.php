<?php
namespace Examples\Transmisson;
require_once (dirname(__FILE__).'/../bootstrap.php');
use SparkPost\SparkPost;

$key = 'YOURAPIKEY';
$sdk = new SparkPost(['key'=>$key]);



$transmission = $sdk->Transmission([
  "campaign"=>"my-campaign",
  "metadata"=>[
   "sample_campaign"=>true,
   "type"=>"these are custom fields"
  ],
  "substitutionData"=>[
    "name"=>"Test Name"
  ],
  "description"=>"my description",
  "return_path"=>"return@example.com",
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

try {
	$results = $transmission->send();
	echo 'Congrats you can use your SDK!';
} catch (\Exception $exception) {
	echo $exception->getMessage();
}
?>