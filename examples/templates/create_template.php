<?php

namespace Examples\Templates;

require dirname(__FILE__).'/../bootstrap.php';

use SparkPost\SparkPost;
use GuzzleHttp\Client;
use Http\Adapter\Guzzle6\Client as GuzzleAdapter;

$httpClient = new GuzzleAdapter(new Client());

/*
 * configure options in example-options.json
 */
$sparky = new SparkPost($httpClient, $options);

$promise = $sparky->request('POST', 'templates', [
  "name" => "PHP example template",
  "content" => [
    "from" => "from@YOUR_DOMAIN",
    "subject" => "Your Subject",
    "html" => "<b>Write your message here.</b>"
  ]
]);

try {
    $response = $promise->wait();
    echo $response->getStatusCode()."\n";
    print_r($response->getBody())."\n";
} catch (\Exception $e) {
    echo $e->getCode()."\n";
    echo $e->getMessage()."\n";
}
