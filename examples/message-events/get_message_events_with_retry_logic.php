<?php

namespace Examples\Templates;

require dirname(__FILE__).'/../bootstrap.php';

use SparkPost\SparkPost;
use GuzzleHttp\Client;
use Http\Adapter\Guzzle6\Client as GuzzleAdapter;

$httpClient = new GuzzleAdapter(new Client());

$sparky = new SparkPost($httpClient, ["key" => "YOUR_API_KEY", "retries" => 3]);

$promise = $sparky->request('GET', 'message-events', [
    'campaign_ids' => 'CAMPAIGN_ID',
]);

/**
 * If this fails with a 5xx it will have failed 4 times
 */
try {
    $response = $promise->wait();
    echo $response->getStatusCode()."\n";
    print_r($response->getBody())."\n";
} catch (\Exception $e) {
    echo $e->getCode()."\n";
    echo $e->getMessage()."\n";

    if ($e->getCode() >= 500 && $e->getCode() <= 599) {
        echo "Wow, this failed epically";
    }
}
