<?php

namespace Examples\Transmisson;

require_once dirname(__FILE__).'/../bootstrap.php';

//pull in API key config
$configFile = file_get_contents(dirname(__FILE__).'/../example-config.json');
$config = json_decode($configFile, true);

use SparkPost\SparkPost;
use GuzzleHttp\Client;
use Ivory\HttpAdapter\Guzzle6HttpAdapter;

$httpAdapter = new Guzzle6HttpAdapter(new Client());
$sparky = new SparkPost($httpAdapter, ['key' => $config['api-key']]);

try {
    $results = $sparky->transmission->send([
        'from'=>[
            'name' => 'From Envelope',
            'email' => 'from@sparkpostbox.com'
        ],
        'html'=>'<p>Hello World!</p>',
        'text'=>'Hello World!',
        'subject'=>'Example Email',
        'recipients'=>[
            [
                'address'=>[
                    'email'=>'john.doe@example.com'
                ]
            ]
        ]
    ]);
    echo 'Congrats! You sent an email using SparkPost!';
} catch (\Exception $exception) {
    echo $exception->getAPIMessage() . "\n";
    echo $exception->getAPICode() . "\n";
    echo $exception->getAPIDescription() . "\n";
}
