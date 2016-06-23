<?php

namespace Examples\Transmissions;

require dirname(__FILE__).'/../bootstrap.php';

use SparkPost\SparkPost;
use GuzzleHttp\Client;
use Http\Adapter\Guzzle6\Client as GuzzleAdapter;

$httpClient = new GuzzleAdapter(new Client());

/*
 * configure options in example-options.json
 */
$sparky = new SparkPost($httpClient, $options);

$promise = $sparky->transmissions->post([
    'content' => [
        'from' => [
            'name' => 'SparkPost Team',
            'email' => 'from@sparkpostbox.com',
        ],
        'subject' => 'First Mailing From PHP',
        'html' => '<html><body><h1>Congratulations, {{name}}!</h1><p>You just sent your very first mailing!</p></body></html>',
        'text' => 'Congratulations, {{name}}!! You just sent your very first mailing!',
    ],
    'substitution_data' => ['name' => 'YOUR_FIRST_NAME'],
    'recipients' => [
        [
            'address' => [
                'name' => 'YOUR_NAME',
                'email' => 'YOUR_EMAIL',
            ],
        ],
    ],
    'cc' => [
        [
            'address' => [
                'name' => 'ANOTHER_NAME',
                'email' => 'ANOTHER_EMAIL',
            ],
        ],
    ],
    'bcc' => [
        [
            'address' => [
                'name' => 'AND_ANOTHER_NAME',
                'email' => 'AND_ANOTHER_EMAIL',
            ],
        ],
    ],
]);

try {
    $response = $promise->wait();
    echo $response->getStatusCode()."\n";
    print_r($response->getBody())."\n";
} catch (\Exception $e) {
    echo $e->getCode()."\n";
    echo $e->getMessage()."\n";
}
