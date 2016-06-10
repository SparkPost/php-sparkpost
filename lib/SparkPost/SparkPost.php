<?php

namespace SparkPost;

use Http\Client\HttpClient;
use Http\Client\HttpAsyncClient;
use GuzzleHttp\Psr7\Request as Request;

class SparkPost
{
    private $version = '2.0.0';
    public $httpClient;
    private $options;

    private static $defaultOptions = [
        'host' => 'api.sparkpost.com',
        'protocol' => 'https',
        'port' => 443,
        'key' => '',
        'version' => 'v1',
        'timeout' => 10,
        'async' => true
    ];

    public $transmissions;

    public function __construct(HttpClient $httpClient, $options)
    {
        $this->setOptions($options);
        $this->setHttpClient($httpClient);
        $this->setupEndpoints();
    }

    public function request($method = 'GET', $uri = '', $payload = [], $headers = []) {
        if ($this->options['async'] === true && $this->httpClient instanceof HttpAsyncClient) {
            $this->asyncRequest($method, $uri, $payload, $headers);
        }
        else {
            $this->syncRequest($method, $uri, $payload, $headers);
        }
    }

    public function syncRequest($method = 'GET', $uri = '', $payload = [], $headers = [])
    {
        $request = $this->buildRequest($method, $uri, $payload, $headers);
        try
        {
            return new SparkPostResponse($this->httpClient->sendRequest($request));
        }
        catch (\Exception $exception)
        {
           throw new SparkPostException($exception);
        }
    }

    public function asyncRequest($method = 'GET', $uri = '', $payload = [], $headers = [])
    {
        if ($this->httpClient instanceof HttpAsyncClient) {
            $request = $this->buildRequest($method, $uri, $payload, $headers);
            return new SparkPostPromise($this->httpClient->sendAsyncRequest($request));
        }
        else {
            throw new Exception('Your http client can not send asynchronous requests.');
        }
    }

    private function buildRequest($method, $uri, $payload, $headers)
    {
        
        $method = trim(strtoupper($method));
        
        if ($method === 'GET'){
            $params = $payload;
            $body = [];
        }
        else {
            $params = [];
            $body = $payload;
        }

        $url = $this->getUrl($uri, $params);
        $headers = $this->getHttpHeaders($headers);

        return new Request($method, $url, $headers, json_encode($body));
    }

    public function getHttpHeaders($headers = [])
    {
        $constantHeaders = [
            'Authorization' => $this->options['key'],
            'Content-Type' => 'application/json'
        ];

        foreach ($constantHeaders as $key => $value) {
            $headers[$key] = $value;
        }

        return $headers;
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

        $this->options = isset($this->options) ? $this->options : self::$defaultOptions;

        // set options, overriding defaults
        foreach ($options as $option => $value) {
            if (key_exists($option, $this->options)) {
                $this->options[$option] = $value;
            }
        }
    }

    private function setupEndpoints() {
        $this->transmissions = new Transmission($this);
    }
}
