<?php

namespace Examples\Templates;

require dirname(__FILE__).'/../bootstrap.php';

use SparkPost\SparkPost;
use GuzzleHttp\Client;
use Http\Adapter\Guzzle6\Client as GuzzleAdapter;

$httpClient = new GuzzleAdapter(new Client());

// In these examples, fetch API key from environment variable
$sparky = new SparkPost($httpClient, ["key" => getenv('SPARKPOST_API_KEY')]);

$template_name = "PHP example template";
$template_id = "PHP-example-template";

// put your own sending domain here
$sending_domain = "steve2-test.trymsys.net";

// Valid short template content examples
$plain_text = 'Write your text message part here.';

$html = <<<HTML
<!DOCTYPE html>
<html lang="en">
<body>
  <p><strong>Write your HTML message part here</strong></p>
</body>
</html>
HTML;

$amp_html = <<<HTML
<!doctype html>
<html ⚡4email>
<head>
  <meta charset="utf-8">
  <style amp4email-boilerplate>body{visibility:hidden}</style>
  <script async src="https://cdn.ampproject.org/v0.js"></script>
</head>
<body>
Hello World! Let's get started using AMP HTML together!
</body>
</html>
HTML;

$promise = $sparky->request('POST', 'templates', [
  'name' => $template_name,
  'id' => $template_id,
  'content' => [
    'from' => "from@$sending_domain",
    'subject' => 'Your Subject',
    'text' => $plain_text,
    'html' => $html,
    'amp_html' => $amp_html,
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
