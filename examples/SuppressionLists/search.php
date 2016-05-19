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
    $results = $sparky->suppression->search([
        "to" => '2016-06-16T09:05:56',
        'from' =>'2015-05-16T09:05:56',
        'limit' => 5,
        'types' => 'transactional'
    ]);
    echo 'Congrats! You retrieved your suppression list from SparkPost!';
} catch (\Exception $exception) {
    echo $exception->getAPIMessage()."\n";
    echo $exception->getAPICode()."\n";
    echo $exception->getAPIDescription()."\n";
}
