<?php

namespace SparkPost;

use GuzzleHttp\Psr7\Request as Request;
use Http\Client\HttpClient;

class SparkPost
{
    private $version = '2.0.0';
    private $config;

    public $httpClient;

    private $options;

    private static $defaultOptions = [
        'host' => 'api.sparkpost.com',
        'protocol' => 'https',
        'port' => 443,
        'strictSSL' => true,
        'key' => '',
        'version' => 'v1',
        'timeout' => 10
    ];

    public function __construct($httpAdapter, $options)
    {
        $this->setOptions($options);
        $this->setHttpAdapter($httpAdapter);
    }

    public function request($method, $uri, $payload = [])
    {
        
        $method = trim(strtoupper($method));
        
        if ($method === 'GET') {
            $params = $payload;
            $body = null;
        }
        else {
            $params = null;
            $body = $payload;
        }

        $url = $this->getUrl($uri, $params);
        $headers = $this->getHttpHeaders();

        $request = new Request($method, $url, $headers, $body);

        return $httpClient->sendRequest($request);
    }

    public function getHttpHeaders()
    {
        return [
            'Authorization' => $this->options['key'],
            'Content-Type' => 'application/json',
        ];
    }

    public function getUrl($uri, $params) {
        return '';
    }

    public function setHttpAdapter(HttpClient $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    public function setOptions($options)
    {
        // if the options map is a string we should assume that its an api key
        if (is_string($options)) {
            $options = ['key' => $options];
        }

        // Validate API key because its required
        if (!isset($this->options['key']) && (!isset($options['key']) || !preg_match('/\S/', $options['key']))) {
            throw new \Exception('You must provide an API key');
        }

        $this->options = $this->options || self::$defaultOptions;

        // set options, overriding defaults
        foreach ($options as $option => $value) {
            if (key_exists($option, $this->options)) {
                $this->options[$option] = $value;
            }
        }
    }
}
