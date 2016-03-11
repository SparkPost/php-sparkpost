<a href="https://www.sparkpost.com"><img src="https://www.sparkpost.com/sites/default/files/attachments/SparkPost_Logo_2-Color_Gray-Orange_RGB.svg" width="200px"/></a>

[Sign up](https://app.sparkpost.com/sign-up?src=Dev-Website&sfdcid=70160000000pqBb) for a SparkPost account and visit our [Developer Hub](https://developers.sparkpost.com) for even more content.

# SparkPost PHP Library

[![Travis CI](https://travis-ci.org/SparkPost/php-sparkpost.svg?branch=master)](https://travis-ci.org/SparkPost/php-sparkpost)
[![Coverage Status](https://coveralls.io/repos/SparkPost/php-sparkpost/badge.svg?branch=master&service=github)](https://coveralls.io/github/SparkPost/php-sparkpost?branch=master) [![Slack Status](http://slack.sparkpost.com/badge.svg)](http://slack.sparkpost.com)

The official PHP library for using [the SparkPost REST API](https://developers.sparkpost.com).

**Note: We understand that the ivory-http-adapter we use in this library is deprecated in favor of httplug. We use Ivory internally to make it simple for you to use whatever HTTP library you want. The deprecation won't affect or limit our ongoing support of this PHP library.**

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
a request adapter to the constructor of the SparkPost Library.  We use the [Ivory HTTP Adapter] (https://github.com/egeloen/ivory-http-adapter) in SparkPost. Please visit their repo
for a list of supported adapters.  If you don't currently use a request library, you will
need to require one and create an adapter from it and pass it along.  The example below uses the
GuzzleHttp Client Library.

An Adapter can be setup like so:

```php
use SparkPost\SparkPost;
use GuzzleHttp\Client;
use Ivory\HttpAdapter\Guzzle6HttpAdapter;

$httpAdapter = new Guzzle6HttpAdapter(new Client());
$sparky = new SparkPost($httpAdapter, ['key'=>'YOUR API KEY']);
```

## Getting Started:  Your First Mailing
For this example to work as is, [Guzzle 6 will need to be installed](http://docs.guzzlephp.org/en/latest/overview.html#installation).  Otherwise another adapter can be used for your specific setup.  See "Setting up a Request Adapter" above.

```php
require 'vendor/autoload.php';

use SparkPost\SparkPost;
use GuzzleHttp\Client;
use Ivory\HttpAdapter\Guzzle6HttpAdapter;

$httpAdapter = new Guzzle6HttpAdapter(new Client());
$sparky = new SparkPost($httpAdapter, ['key'=>'YOUR API KEY']);

try {
  // Build your email and send it!
  $results = $sparky->transmission->send([
    'from'=>'From Envelope <from@sparkpostbox.com>',
    'html'=>'<html><body><h1>Congratulations, {{name}}!</h1><p>You just sent your very first mailing!</p></body></html>',
    'text'=>'Congratulations, {{name}}!! You just sent your very first mailing!',
    'substitutionData'=>['name'=>'YOUR FIRST NAME'],
    'subject'=>'First Mailing From PHP',
    'recipients'=>[
      [
        'address'=>[
          'name'=>'YOUR FULL NAME',
          'email'=>'YOUR EMAIL ADDRESS'
        ]
      ]
    ]
  ]);
  echo 'Woohoo! You just sent your first mailing!';
} catch (\Exception $err) {
  echo 'Whoops! Something went wrong';
  var_dump($err);
}
```

## Learn More
* For more detailed examples, check our examples:
  * [Transmissions](https://github.com/SparkPost/php-sparkpost/tree/master/examples/transmission)
* Read our REST API documentation - <http://www.sparkpost.com/docs/introduction>

## Field Descriptions
### Transmissions
| Field Name       | Required?   | Description                                                                                                                | Data Type        |
| ------------     | ----------- | -------------                                                                                                              | -----------      |
| description      | no          | Field for describing what this transmission is for the user                                                                | String           |
| campaign         | no          | Field for assigning a given transmission to a specific campaign, which is a logical container for similar transmissions    | String           |
| metadata         | no          | Field for adding arbitrary key/value pairs which will be included in open/click tracking                                   | Object (Simple)  |
| substitutionData | no          | Field for adding transmission level substitution data, which can be used in a variety of fields and in content             | Object (Complex) |
| trackOpens       | no          | Field for enabling/disabling transmission level open tracking  (default: true)                                             | Boolean          |
| trackClicks      | no          | Field for enabling/disabling transmission level click tracking (default: true)                                             | Boolean          |
| useDraftTemplate | no          | Field for allowing the sending of a transmission using a draft of a stored template (default: false)                       | Boolean          |
| replyTo          | no          | Field for specifying the email address that should be used when a recipient hits the reply button                          | String           |
| subject          | yes         | Field for setting the subject line of a given transmission                                                                 | String           |
| from             | yes**       | Field for setting the from line of a given transmission                                                                    | String or Object |
| html             | yes**       | Field for setting the HTML content of a given transmission                                                                 | String           |
| text             | yes**       | Field for setting the Plain Text content of a given transmission                                                           | String           |
| rfc822           | no**        | Field for setting the RFC-822 encoded content of a given transmission                                                      | String           |
| template         | no**        | Field for specifying the Template ID of a stored template to be used when sending a given transmission                     | String           |
| customHeaders    | no          | Field for specifying additional headers to be applied to a given transmission (other than Subject, From, To, and Reply-To) | Object (Simple)  |
| recipients       | yes**       | Field for specifying who a given transmission should be sent to                                                            | Array of Objects |
| recipientList    | no**        | Field for specifying a stored recipient list ID to be used for a given transmission                                        | String           |

** - If using inline content then html or text are required. If using RFC-822 Inline Content, then rfc822 is required. If using a stored recipient list, then recipientList is required. If using a stored template, then template is required but from is not as the values from the template will be used.

## Tips and Tricks
### General
* You _must_ provide at least an API key when instantiating the SparkPost Library - `[ 'key'=>'184ac5480cfdd2bb2859e4476d2e5b1d2bad079bf' ]`
* The library's features are namespaced under the various SparkPost API names.

### Transmissions
* If you specify a stored recipient list and inline recipients in a Transmission, you will receive an error.
* If you specify HTML and/or Plain Text content and then provide RFC-822 encoded content, you will receive an error.
    * RFC-822 content is not valid with any other content type.
* If you specify a stored template and also provide inline content via `html` or `text`, you will receive an error.
* By default, open and click tracking are enabled for a transmission.
* By default, a transmission will use the published version of a stored template.

### Contributing
See [contributing](https://github.com/SparkPost/php-sparkpost/blob/master/CONTRIBUTING.md).
