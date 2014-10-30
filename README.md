# SparkPost PHP SDK
=======
The official PHP binding for your favorite SparkPost APIs!

Before using this library, you must have a valid API Key.

To get an API Key, please log in to your SparkPost account and generate one in the Settings page.

## Installation
============
The recommended way to install the SparkPost PHP SDK is through composer.
```
# Install Composer
curl -sS https:// getcomposer.org/installer | php
```
Next, run the Composer command to install the SparkPost PHP SDK: 
```
composer require messagesystems/php-sdk
```
After installing, you need to require Composer's autoloader:
```
require 'vendor/autoload.php';
```

## Getting Started:  Your First Mailing
```
$sparkpost = new SparkPost(["key"=>"YOUR API KEY"]);

$transmission = $sparkpost->transmission();

// Add some template data to your email
$transmission->setCampaign('first-mailing')->
	setReturnPath('bounces@sparkpost.com')->
    setFrom('you@your-company.com')->
    setSubject('First SDK Mailing')->
    setHTMLContent('<html><body><h1>Congratulations, {{name}}!</h1><p>You just sent your very first mailing!</p></body></html>')->
    setTextContent('Congratulations, {{name}}!! You just sent your very first mailing!')->
    setSubstitutionData(['name'=>'YOUR FIRST NAME']);
    
// Pick someone to receive your email
$transmission->addRecipient(['address'=>['name'=>'YOUR FULL NAME', 'email'=>'YOUR EMAIL ADDRESS' ]]);

// Send it off into the world!
try {
	$response = $transmission->send();	
    echo 'Woohoo! You just sent your first mailing!';
} catch (Exception $err) {
    echo 'Whoops! Something went wrong';
    var_dump($err);
}
```

## Learn More
* For more detailed examples, check our examples:
    * [Transmissions](https://github.com/MessageSystems/php-sdk/tree/master/examples/transmission/)
* Read our REST API documentation - <http://docs.thecloudplaceholderapiv1.apiary.io/>

## Tips and Tricks
### General
* You _must_ provide at least an API key when instantiating the SparkPost Library - `[ 'key'=>'184ac5480cfdd2bb2859e4476d2e5b1d2bad079bf' ]`
* The SDK's features are namespaced under the various SparkPost API names.
* There are two ways to provide values to each namespace of the SDK:
    - On instantiation, you pass in a well-formed object (See examples).
    - You use the helper methods to incrementally create a well-formed object. These helper methods are chainable (See examples).

### Transmissions
* If you specify a stored recipient list and inline recipients in a Transmission, whichever was provided last will be used.
    * If you call addRecipient and then useRecipientList, the latter will overwrite the former.
* If you specify HTML and/or Plain Text content and then provide RFC-822 encoded content, you will receive an error.
    * RFC-822 content is not valid with any other content type.
* If you specify a stored template and also provide inline content via setHTMLContent or setTextContent, you will receive an error.
* By default, open and click tracking are enabled for a transmission.
* By default, a transmission will use the published version of a stored template.

## Development

### Setup
We use [Robo](http://robo.li/) for our task runner.

Run `composer install` inside the directory to install dependecies and development tools including Robo.

### Testing
Once all the dependencies are installed, you can execute the unit tests using `vendor\bin\robo test`

### Contributing
Guidelines for adding issues

Submitting pull requests

Signing our CLA

Our coding standards