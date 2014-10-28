<?php
namespace Examples\Transmisson;
require_once (dirname(__FILE__).'/../bootstrap.php');
use SparkPost\SparkPost;

$key = 'YOURAPIKEY';
$sdk = new SparkPost(['key'=>$key]);



$transmission = $sdk->Transmission();

$transmission->setReturnPath('return@example.com')
  ->addRecipient([
      'address'=>[
        'email'=>'john.doe@sample.com'
      ]
  ])
  ->setRfc822Content("Content-Type: text/plain\nFrom: From Envelope <from@example.com>\nSubject: Example Email\n\nHello World");


try {
	$results = $transmission->send();
	echo 'Congrats you can use your SDK!';
} catch (\Exception $exception) {
	echo $exception->getMessage();
}
?>