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

    public function __construct(HttpClient $httpClient, $options)
    {
        $this->setOptions($options);
        $this->setHttpClient($httpClient);
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

        return $httpClient->sendAsyncRequest($request);
    }

    public function getHttpHeaders()
    {
        return [
            'Authorization' => $this->options['key'],
            'Content-Type' => 'application/json',
        ];
    }

    public function getUrl($path, $params) {
        $options = $this->options;

        for ($index = 0; $index < count($params); $index++) { 
            if (is_array($params[$index]))
                $params[$index] = implode(',', $params);
        }
        $paramsString = http_build_query($params);

        return $options['protocol'].'://'.$options['host'].($options['port'] ? ':'.$options['port'] : '').'/api/'.$options['version'].'/'.$path.($paramsString ? '?'.$paramsString : '');
    }

    public function setHttpClient(HttpClient $httpClient)
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
