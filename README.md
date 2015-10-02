[![Travis CI](https://travis-ci.org/SparkPost/php-sparkpost.svg?branch=master)](https://travis-ci.org/SparkPost/php-sparkpost)
[![Coverage Status](https://coveralls.io/repos/SparkPost/php-sparkpost/badge.svg?branch=master&service=github)](https://coveralls.io/github/SparkPost/php-sparkpost?branch=master)

# SparkPost PHP SDK
The official PHP binding for your favorite SparkPost APIs!

Before using this library, you must have a valid API Key.

To get an API Key, please log in to your SparkPost account and generate one in the Settings page.

## Installation
The recommended way to install the SparkPost PHP SDK is through composer.
```
# Install Composer
curl -sS https://getcomposer.org/installer | php
```
Next, run the Composer command to install the SparkPost PHP SDK:
```
composer require sparkpost/php-sparkpost
```
After installing, you need to require Composer's autoloader:
```
require 'vendor/autoload.php';
use SparkPost\SparkPost;
```

## Getting Started:  Your First Mailing
```
SparkPost::setConfig(["key"=>"YOUR API KEY"]);

try {
	// Build your email and send it!
	Transmission::send(array('campaign'=>'first-mailing',
		'from'=>'you@your-company.com',
	    'subject'=>'First SDK Mailing',
	    'html'=>'<html><body><h1>Congratulations, {{name}}!</h1><p>You just sent your very first mailing!</p></body></html>',
	    'text'=>'Congratulations, {{name}}!! You just sent your very first mailing!',
	    'substitutionData'=>array('name'=>'YOUR FIRST NAME'),
	    'recipients'=>array(array('address'=>array('name'=>'YOUR FULL NAME', 'email'=>'YOUR EMAIL ADDRESS' )))
    ));

    echo 'Woohoo! You just sent your first mailing!';
} catch (Exception $err) {
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
| from             | yes         | Field for setting the from line of a given transmission                                                                    | String or Object |
| html             | yes**       | Field for setting the HTML content of a given transmission                                                                 | String           |
| text             | yes**       | Field for setting the Plain Text content of a given transmission                                                           | String           |
| rfc822           | no**        | Field for setting the RFC-822 encoded content of a given transmission                                                      | String           |
| template         | no**        | Field for specifying the Template ID of a stored template to be used when sending a given transmission                     | String           |
| customHeaders    | no          | Field for specifying additional headers to be applied to a given transmission (other than Subject, From, To, and Reply-To) | Object (Simple)  |
| recipients       | yes**       | Field for specifying who a given transmission should be sent to                                                            | Array of Objects |
| recipientList    | no**        | Field for specifying a stored recipient list ID to be used for a given transmission                                        | String           |

** - If using inline content then html or text are required. If using RFC-822 Inline Content, then rfc822 is required. If using a stored recipient list, then recipientList is required. If using a stored template, then template is required.

## Tips and Tricks
### General
* You _must_ provide at least an API key when instantiating the SparkPost Library - `[ 'key'=>'184ac5480cfdd2bb2859e4476d2e5b1d2bad079bf' ]`
* The SDK's features are namespaced under the various SparkPost API names.

### Transmissions
* If you specify a stored recipient list and inline recipients in a Transmission, you will recieve an error.
* If you specify HTML and/or Plain Text content and then provide RFC-822 encoded content, you will receive an error.
    * RFC-822 content is not valid with any other content type.
* If you specify a stored template and also provide inline content via `html` or `text`, you will receive an error.
* By default, open and click tracking are enabled for a transmission.
* By default, a transmission will use the published version of a stored template.

## Development

### Setup
Run `composer install` inside the directory to install dependecies and development tools.

### Testing
Once all the dependencies are installed, you can execute the unit tests using:
```
composer test
```

### Contributing
1. Check for open issues or open a fresh issue to start a discussion around a feature idea or a bug.
2. Fork [the repository](http://github.com/SparkPost/php-sparkpost) on GitHub to start making your changes to the **master** branch (or branch off of it).
3. Write a test which shows that the bug was fixed or that the feature works as expected.
4. Send a pull request and bug the maintainer until it gets merged and published. :) Make sure to add yourself to [AUTHORS](https://github.com/SparkPost/php-sparkpost/blob/master/AUTHORS.md).
