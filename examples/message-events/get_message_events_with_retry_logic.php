<?php

namespace Examples\Templates;

require dirname(__FILE__).'/../bootstrap.php';

use SparkPost\SparkPost;
use GuzzleHttp\Client;
use Http\Adapter\Guzzle6\Client as GuzzleAdapter;

$httpClient = new GuzzleAdapter(new Client());

// In these examples, fetch API key from environment variable
$sparky = new SparkPost($httpClient, ["key" => getenv('SPARKPOST_API_KEY'), "retries" => 3]);

// New endpoint - https://developers.sparkpost.com/api/events/
$promise = $sparky->request('GET', 'events/message', [
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
