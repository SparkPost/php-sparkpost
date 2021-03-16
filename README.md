<a href="https://www.sparkpost.com"><img src="https://www.sparkpost.com/sites/default/files/attachments/SparkPost_Logo_2-Color_Gray-Orange_RGB.svg" width="200px"/></a>

[Sign up](https://app.sparkpost.com/join?plan=free-0817?src=Social%20Media&sfdcid=70160000000pqBb&pc=GitHubSignUp&utm_source=github&utm_medium=social-media&utm_campaign=github&utm_content=sign-up) for a SparkPost account and visit our [Developer Hub](https://developers.sparkpost.com) for even more content.

# SparkPost PHP Library

[![Travis CI](https://travis-ci.org/SparkPost/php-sparkpost.svg?branch=master)](https://travis-ci.org/SparkPost/php-sparkpost)
[![Coverage Status](https://coveralls.io/repos/SparkPost/php-sparkpost/badge.svg?branch=master&service=github)](https://coveralls.io/github/SparkPost/php-sparkpost?branch=master)
[![Downloads](https://img.shields.io/packagist/dt/sparkpost/sparkpost.svg?maxAge=3600)](https://packagist.org/packages/sparkpost/sparkpost)
[![Packagist](https://img.shields.io/packagist/v/sparkpost/sparkpost.svg?maxAge=3600)](https://packagist.org/packages/sparkpost/sparkpost)

The official PHP library for using [the SparkPost REST API](https://developers.sparkpost.com/api/).

Before using this library, you must have a valid API Key. To get an API Key, please log in to your SparkPost account and generate one in the Settings page.

## Installation
**Please note: The composer package `sparkpost/php-sparkpost` has been changed to `sparkpost/sparkpost` starting with version 2.0.**

The recommended way to install the SparkPost PHP Library is through composer.

```
# Install Composer
curl -sS https://getcomposer.org/installer | php
```

Sparkpost requires php-http client (see [Setting up a Request Adapter](#setting-up-a-request-adapter)). There are several [providers](https://packagist.org/providers/php-http/client-implementation) available. If you were using guzzle6 your install might look like this.

```
composer require php-http/guzzle6-adapter "^1.1"
composer require guzzlehttp/guzzle "^6.0"
```

Next, run the Composer command to install the SparkPost PHP Library:

```
composer require sparkpost/sparkpost
```

After installing, you need to require Composer's autoloader:

```php
require 'vendor/autoload.php';
use SparkPost\SparkPost;
```

**Note:** Without composer the costs outweigh the benefits of using the PHP client library. A simple function like the one in [issue #164](https://github.com/SparkPost/php-sparkpost/issues/164#issuecomment-289888237) wraps the SparkPost API and makes it easy to use the API without resolving the composer dependencies.

## Running with IDEs

When running with `xdebug` under an IDE such as VS Code, you may see an exception is thrown in file `vendor/php-http/discovery/src/Strategy/PuliBetaStrategy.php`:

```
Exception has occurred.
Http\Discovery\Exception\PuliUnavailableException: Puli Factory is not available
```

[This is usual](http://docs.php-http.org/en/latest/discovery.html#puli-factory-is-not-available). Puli is not required to use the library. You can resume running after the exception.

You can prevent the exception, by setting the discovery strategies, prior to creating the adapter object:
```php
// Prevent annoying "Puli exception" during work with xdebug / IDE
// See https://github.com/getsentry/sentry-php/issues/801
\Http\Discovery\ClassDiscovery::setStrategies([
        // \Http\Discovery\Strategy\PuliBetaStrategy::class, // Deliberately disabled
        \Http\Discovery\Strategy\CommonClassesStrategy::class,
        \Http\Discovery\Strategy\CommonPsr17ClassesStrategy::class,
]);
```

## Setting up a Request Adapter

Because of dependency collision, we have opted to use a request adapter rather than
requiring a request library.  This means that your application will need to pass in
a request adapter to the constructor of the SparkPost Library.  We use the [HTTPlug](https://github.com/php-http/httplug) in SparkPost. Please visit their repo for a list of supported [clients and adapters](http://docs.php-http.org/en/latest/clients.html).  If you don't currently use a request library, you will
need to require one and create a client from it and pass it along. The example below uses the GuzzleHttp Client Library.

A Client can be setup like so:

```php
<?php
require 'vendor/autoload.php';

use SparkPost\SparkPost;
use GuzzleHttp\Client;
use Http\Adapter\Guzzle6\Client as GuzzleAdapter;

$httpClient = new GuzzleAdapter(new Client());
$sparky = new SparkPost($httpClient, ['key'=>'YOUR_API_KEY']);
?>
```

## Initialization
#### new Sparkpost(httpClient, options)
* `httpClient`
    * Required: Yes
    * HTTP client or adapter supported by HTTPlug
* `options`
    * Required: Yes
    * Type: `String` or `Array`
    * A valid Sparkpost API key or an array of options
* `options.key`
    * Required: Yes
    * Type: `String`
    * A valid Sparkpost API key
* `options.host`
    * Required: No
    * Type: `String`
    * Default: `api.sparkpost.com`
* `options.protocol`
    * Required: No
    * Type: `String`
    * Default: `https`
* `options.port`
    * Required: No
    * Type: `Number`
    * Default: 443
* `options.version`
    * Required: No
    * Type: `String`
    * Default: `v1`
* `options.async`
    * Required: No
    * Type: `Boolean`
    * Default: `true`
    * `async` defines if the `request` function sends an asynchronous or synchronous request. If your client does not support async requests set this to `false`
* `options.retries`
    * Required: No
    * Type: `Number`
    * Default: `0`
    * `retries` controls how many API call attempts the client makes after receiving a 5xx response
* `options.debug`
    * Required: No
    * Type: `Boolean`
    * Default: `false`
    * If `debug` is true, then all `SparkPostResponse` and `SparkPostException` instances will return any array of the request values through the function `getRequest`

## Methods
### request(method, uri [, payload [, headers]])
* `method`
    * Required: Yes
    * Type: `String`
    * HTTP method for request
* `uri`
    * Required: Yes
    * Type: `String`
    * The URI to receive the request
* `payload`
    * Required: No
    * Type: `Array`
    * If the method is `GET` the values are encoded into the URL. Otherwise, if the method is `POST`, `PUT`, or `DELETE` the payload is used for the request body.
* `headers`
    * Required: No
    * Type: `Array`
    * Custom headers to be sent with the request.

### syncRequest(method, uri [, payload [, headers]])
Sends a synchronous request to the SparkPost API and returns a `SparkPostResponse`

### asyncRequest(method, uri [, payload [, headers]])
Sends an asynchronous request to the SparkPost API and returns a `SparkPostPromise`

### setHttpClient(httpClient)
* `httpClient`
    *  Required: Yes
    * HTTP client or adapter supported by HTTPlug

### setOptions(options)
* `options`
    *  Required: Yes
    *  Type: `Array`
    * See constructor

## Endpoints
### transmissions
* **post(payload)**
    * `payload` - see request options
    * `payload.cc`
        * Required: No
        * Type: `Array`
        * Recipients to receive a carbon copy of the transmission
    * `payload.bcc`
        * Required: No
        * Type: `Array`
        * Recipients to discreetly receive a carbon copy of the transmission

## Examples

### Send An Email Using The Transmissions Endpoint
```php
<?php
require 'vendor/autoload.php';

use SparkPost\SparkPost;
use GuzzleHttp\Client;
use Http\Adapter\Guzzle6\Client as GuzzleAdapter;

$httpClient = new GuzzleAdapter(new Client());
// Good practice to not have API key literals in code - set an environment variable instead
// For simple example, use synchronous model
$sparky = new SparkPost($httpClient, ['key' => getenv('SPARKPOST_API_KEY'), 'async' => false]);

try {
    $response = $sparky->transmissions->post([
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
    } catch (\Exception $error) {
        var_dump($error);
    }
print($response->getStatusCode());
$results = $response->getBody()['results'];
var_dump($results);
?>
```

More examples [here](./examples/):
### [Transmissions](./examples/transmissions/)
- Create with attachment
- Create with recipient list
- Create with cc and bcc
- Create with template
- Create
- Delete (scheduled transmission by campaign_id *only*)

### [Templates](./examples/templates/)
- Create
- Get
- Get (list) all
- Update
- Delete

### [Message Events](./examples/message-events/)
- get
- get (with retry logic)

### Send An API Call Using The Base Request Function

We provide a base request function to access any of our API resources.
```php
<?php
require 'vendor/autoload.php';

use SparkPost\SparkPost;
use GuzzleHttp\Client;
use Http\Adapter\Guzzle6\Client as GuzzleAdapter;

$httpClient = new GuzzleAdapter(new Client());
$sparky = new SparkPost($httpClient, [
    'key' => getenv('SPARKPOST_API_KEY'),
    'async' => false]);

$webhookId = 'afd20f50-865a-11eb-ac38-6d7965d56459';
$response = $sparky->request('DELETE', 'webhooks/' . $webhookId);
print($response->getStatusCode());
?>
```

> Be sure to not have a leading `/` in your resource URI.

For complete list of resources, refer to [API documentation](https://developers.sparkpost.com/api/).

## Handling Responses
The API calls either return a `SparkPostPromise` or `SparkPostResponse` depending on if `async` is `true` or `false`

### Synchronous
```php
$sparky->setOptions(['async' => false]);
try {
    $response = ... // YOUR API CALL GOES HERE

    echo $response->getStatusCode()."\n";
    print_r($response->getBody())."\n";
}
catch (\Exception $e) {
    echo $e->getCode()."\n";
    echo $e->getMessage()."\n";
}
```

### Asynchronous
Asynchronous an be handled in two ways: by passing callbacks or waiting for the promise to be fulfilled. Waiting acts like synchronous request.
##### Wait (Synchronous)
```php

$promise = ... // YOUR API CALL GOES HERE

try {
    $response = $promise->wait();
    echo $response->getStatusCode()."\n";
    print_r($response->getBody())."\n";
} catch (\Exception $e) {
    echo $e->getCode()."\n";
    echo $e->getMessage()."\n";
}

echo "I will print out after the promise is fulfilled";
```

##### Then (Asynchronous)
```php
$promise = ... // YOUR API CALL GOES HERE

$promise->then(
    // Success callback
    function ($response) {
        echo $response->getStatusCode()."\n";
        print_r($response->getBody())."\n";
    },
    // Failure callback
    function (Exception $e) {
        echo $e->getCode()."\n";
        echo $e->getMessage()."\n";
    }
);

echo "I will print out before the promise is fulfilled";

// You can combine multiple promises using \GuzzleHttp\Promise\all() and other functions from the library.
$promise->wait();
```

## Handling Exceptions
An exception will be thrown in two cases: there is a problem with the request or  the server returns a status code of `400` or higher.

### SparkPostException
* **getCode()**
    * Returns the response status code of `400` or higher
* **getMessage()**
    * Returns the exception message
* **getBody()**
    * If there is a response body it returns it as an `Array`. Otherwise it returns `null`.
* **getRequest()**
    * Returns an array with the request values `method`, `url`, `headers`, `body` when `debug` is `true`


### Contributing
See [contributing](https://github.com/SparkPost/php-sparkpost/blob/master/CONTRIBUTING.md).
