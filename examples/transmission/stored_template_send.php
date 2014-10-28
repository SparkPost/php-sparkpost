<?php
namespace Examples\Transmisson;
require_once (dirname(__FILE__).'/../bootstrap.php');
use SparkPost\SparkPost;


$key = 'YOURAPIKEY';
$sdk = new SparkPost(['key'=>$key]);



$transmission = $sdk->Transmission();

$transmission->setReturnPath('return@example.com')
  ->setFrom('From Envelope <from@example.com>')
  ->addRecipient([
      "address"=>[
        "email"=>"john.doe@sample.com"
      ]
  ])
  ->useStoredTemplate('my-template');

try {
	$results = $transmission->send();
	echo 'Congrats you can use your SDK!';
} catch (\Exception $exception) {
	echo $exception->getMessage();
}
?>