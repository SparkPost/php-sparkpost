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
    'campaign' => 'my-campaign',
    'metadata' => [
      'sample_campaign' => true,
      'type' => 'these are custom fields',
    ],
    'substitutionData' => [
      'name' => 'Test Name',
    ],
    'description' => 'my description',
    'replyTo' => 'reply@test.com',
    'customHeaders' => [
      'X-Custom-Header' => 'Sample Custom Header',
    ],
    'trackOpens' => false,
    'trackClicks' => false,
    'from' => 'From Envelope <from@sparkpostbox.com>',
    'html' => '<p>Hello World! Your name is: {{name}}</p>',
    'text' => 'Hello World!',
    'subject' => 'Example Email: {{name}}',
    'recipients' => [
      [
        'address' => [
          'email' => 'john.doe@example.com',
        ],
      ],
    ],
  ]);

    echo 'Congrats you can use your SDK!';
} catch (\Exception $exception) {
    echo $exception->getMessage();
}
