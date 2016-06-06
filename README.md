<a href="https://www.sparkpost.com"><img src="https://www.sparkpost.com/sites/default/files/attachments/SparkPost_Logo_2-Color_Gray-Orange_RGB.svg" width="200px"/></a>

[Sign up](https://app.sparkpost.com/sign-up?src=Dev-Website&sfdcid=70160000000pqBb) for a SparkPost account and visit our [Developer Hub](https://developers.sparkpost.com) for even more content.

# SparkPost PHP Library

[![Travis CI](https://travis-ci.org/SparkPost/php-sparkpost.svg?branch=master)](https://travis-ci.org/SparkPost/php-sparkpost)
[![Coverage Status](https://coveralls.io/repos/SparkPost/php-sparkpost/badge.svg?branch=master&service=github)](https://coveralls.io/github/SparkPost/php-sparkpost?branch=master) [![Slack Status](http://slack.sparkpost.com/badge.svg)](http://slack.sparkpost.com)

The official PHP library for using [the SparkPost REST API](https://developers.sparkpost.com).

Before using this library, you must have a valid API Key. To get an API Key, please log in to your SparkPost account and generate one in the Settings page.

## Installation
The recommended way to install the SparkPost PHP Library is through composer.

```
# Install Composer
curl -sS https://getcomposer.org/installer | php
```

Next, run the Composer command to install the SparkPost PHP Library:

```
composer require sparkpost/php-sparkpost
```

After installing, you need to require Composer's autoloader:

```php
require 'vendor/autoload.php';
use SparkPost\SparkPost;
```

## Setting up a Request Adapter

Because of dependency collision, we have opted to use a request adapter rather than
requiring a request library.  This means that your application will need to pass in
a request adapter to the constructor of the SparkPost Library.  We use the [HTTPlug](https://github.com/php-http/httplug) in SparkPost. Please visit their repo for a list of supported adapters.  If you don't currently use a request library, you will
need to require one and create an adapter from it and pass it along. The example below uses the GuzzleHttp Client Library.

An Adapter can be setup like so:

```php
<?php
use SparkPost\SparkPost;
use GuzzleHttp\Client;
use Http\Adapter\Guzzle6\Client as GuzzleAdapter;

$httpAdapter = new GuzzleAdapter(new Client());
$sparky = new SparkPost($httpAdapter, ['key'=>'YOUR API KEY']);
?>
```

## Initialization
#### new Sparkpost(httpAdapter, options)
* `httpAdapter`
    * Required: Yes
    * HTTP client or adapter supported by HTTPlug
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
* `options.strictSSL`
    * Required: No
    * Type: `Boolean`
    * Default: `true`
* `options.version`
    * Required: No
    * Type: `String`
    * Default: `v1`
* `options.timeout`
    * Required: No
    * Type: `Number`
    * Default: `10`

## Methods
### request(method, uri [, payload])
* `method`
    * Required: Yes
    * Type: `String`
    * HTTP method for request
* `uri`
    * Required: Yes
    * Type: `String`
    * The URI to recieve the request
* `payload`
    * Required: No
    * Type: `Array`
    * If the method is `GET` the values are encoded into the URL. Otherwise, if the method is `POST`, `PUT`, or `DELETE` the payload is used for the request body.

#### Handling Response
The request function returns a promise. You can wait for the promise or you can handle it asynchronously.

##### Wait
```php
<?php
try {
    $response = $promise->wait();
} catch (Exception $e) {
    echo $exception->getMessage();
}
?>
```

##### Asynchronously
```php
<?php
$promise->then(
    // Success callback
    function ($response) {
        echo $response->getStatusCode();
        echo $response->getBody();
    },
    // Failure callback
    function (\Exception $e) {
        echo $exception->getMessage();
    }
);
?>
```

### setHttpAdapter(httpAdapter)
* `httpAdapter`
    *  Required: Yes
    * HTTP client or adapter supported by HTTPlug

## Endpoints
### transmission
* **get([transmissionID] [, payload])**
    * `transmissionID` - see `uri` request options
    * `payload` - see request options
* **post(payload)**
    * `payload` - see request options
    * `payload.cc`
        * Required: No
        * Type: `Array`
        * Recipients to recieve a carbon copy of the transmission
    * `payload.bcc`
        * Required: No
        * Type: `Array`
        * Recipients to descreetly recieve a carbon copy of the transmission
* **delete(transmissionID)**
    * `transmissionID` - see `uri` request options
    * `payload` - see request options

### Contributing
See [contributing](https://github.com/SparkPost/php-sparkpost/blob/master/CONTRIBUTING.md).