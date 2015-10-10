<?php
namespace Examples\Transmisson;
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
  $results = $sparky->transmission->send([
    'from'=>'From Envelope <from@sparkpostbox.com>',
    'recipients'=>[
      [
        'address'=>[
          'email'=>'john.doe@example.com'
        ]
      ]
    ],
    'template'=>'my-first-email'
  ]);
  echo 'Congrats you can use your SDK!';
} catch (\Exception $exception) {
  echo $exception->getMessage();
}
?>
