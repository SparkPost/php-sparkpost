<?php

namespace Examples\SuppressionHandler;
ini_set('display_errors', 'on');
error_reporting(E_ALL);
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
    $results = $sparky->suppression->deleteAddress('rcpt_1@example.com');
    echo 'Congrats! You have deleted an address from SparkPost Suppression List!';
} catch (\Exception $exception) {
    echo "Failed";
    echo $exception->getAPIMessage()."\n";
    echo $exception->getAPICode()."\n";
    echo $exception->getAPIDescription()."\n";
}
