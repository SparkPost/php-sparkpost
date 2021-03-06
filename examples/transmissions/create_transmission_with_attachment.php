<?php

namespace Examples\Transmissions;

require dirname(__FILE__).'/../bootstrap.php';

use SparkPost\SparkPost;
use GuzzleHttp\Client;
use Http\Adapter\Guzzle6\Client as GuzzleAdapter;

$httpClient = new GuzzleAdapter(new Client());

// In these examples, fetch API key from environment variable
$sparky = new SparkPost($httpClient, ["key" => getenv('SPARKPOST_API_KEY')]);

$filePath = dirname(__FILE__).'/';
$fileName = 'sparkpost.png';
$fileType = mime_content_type($filePath.$fileName);
$fileData = base64_encode(file_get_contents($filePath.$fileName));

// put your own sending domain and test recipient address here
$sending_domain = "steve2-test.trymsys.net";
$your_email = "bob@sink.sparkpostmail.com";

$promise = $sparky->transmissions->post([
    'content' => [
        'from' => [
            'name' => 'SparkPost Team',
            'email' => "from@$sending_domain",
        ],
        'subject' => 'Mailing With Attachment From PHP',
        'html' => '<html><body><h1>Congratulations, {{name}}!</h1><p>You just sent an email with an attachment!</p></body></html>',
        'text' => 'Congratulations, {{name}}! You just sent an email with an attachment',
        'attachments' => [
            [
                'name' => $fileName,
                'type' => $fileType,
                'data' => $fileData,
            ],
        ],
    ],
    'substitution_data' => ['name' => 'YOUR_FIRST_NAME'],
    'recipients' => [
        [
            'address' => [
                'name' => 'YOUR_NAME',
                'email' => $your_email,
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
