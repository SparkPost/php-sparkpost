<?php

namespace Examples\SuppressionHandler;

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
    $results = $sparky->suppression->insert([
        "recipients" => [
            [
                "email" => "rcpt_1@example.com",
                "transactional" => true,
                "description" => "User requested to not receive any transactional emails."
            ],
            [
                "email" => "rcpt_2@example.com",
                "transactional" => false,
                "description" => "User requested to not receive any non transactional emails."
            ]
        ]
    ]);
    echo 'Congrats! You added a user to your suppression list!';
} catch (\Exception $exception) {
    echo $exception->getAPIMessage()."\n";
    echo $exception->getAPICode()."\n";
    echo $exception->getAPIDescription()."\n";
}
