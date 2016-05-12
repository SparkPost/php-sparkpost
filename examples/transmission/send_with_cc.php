<?php

/*
 * For a more detailed explanation of how cc/bcc work with SparkPost, please
 * check out this article: https://support.sparkpost.com/customer/portal/articles/1948014
 */

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
        'from' => [
            'name' => 'From Envelope',
            'email' => 'from@sparkpostbox.com',
        ],
        'html' => '<p>An example email using cc with SparkPost to the {{recipient_type}} recipient.</p>',
        'text' => 'An example email using cc with SparkPost to the {{recipient_type}} recipient.',
        'subject' => 'Example email using cc',
        'recipients' => [
            [
                'address' => [
                    'name' => 'Original Recipient',
                    'email' => 'original.recipient@example.com',
                ],
                'substitution_data' => [
                    'recipient_type' => 'Original'
                ]
            ],
            [
                'address' => [
                    'name' => 'Carbon Copy Recipient',
                    'email' => 'cc.recipient@example.com',
                    'header_to' => '"Original Recipient" <original.recipient@example.com>',
                ],
                'substitution_data' => [
                    'recipient_type' => 'CC'
                ]
            ],
        ],
        'customHeaders' => [
            'CC' => '"Carbon Copy Recipient" <cc.recipient@example.com>'
        ]
    ]);
    echo 'Congrats! You sent an email with cc using SparkPost!';
} catch (\Exception $exception) {
    echo $exception->getAPIMessage()."\n";
    echo $exception->getAPICode()."\n";
    echo $exception->getAPIDescription()."\n";
}
