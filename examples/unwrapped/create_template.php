<?php
namespace Examples\Unwrapped;
require_once (dirname(__FILE__).'/../bootstrap.php');

//pull in API key config
$configFile = file_get_contents(dirname(__FILE__) . '/../example-config.json');
$config = json_decode($configFile, true);

use SparkPost\SparkPost;
use GuzzleHttp\Client;
use Ivory\HttpAdapter\Guzzle6HttpAdapter;

$httpAdapter = new Guzzle6HttpAdapter(new Client());
$sparky = new SparkPost($httpAdapter, ['key'=>$config['api-key']]);

try {
  // define the endpoint
  $sparky->setupUnwrapped('templates');

  $templateConfig = [
    'name' => 'Summer Sale!',
    'id'=>'summer-sale',
    'content'=> [
      'from' => 'from@sparkpostbox.com',
      'subject' => 'Summer deals',
      'html' => '<b>Check out these deals!</b>'
    ]
  ];
  $results = $sparky->templates->create($templateConfig);
  echo 'Congrats you can use your SDK!';
} catch (\Exception $exception) {
  echo $exception->getMessage();
}
?>
