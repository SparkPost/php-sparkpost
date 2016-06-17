<?php

namespace SparkPost;

class Resource
{
    protected $sparkpost;
    protected $endpoint;

    public function __construct(SparkPost $sparkpost, $endpoint)
    {
        $this->sparkpost = $sparkpost;
        $this->endpoint = $endpoint;
    }

    public function get($uri, $payload, $headers)
    {
        return $this->request('GET', $uri, $payload, $headers);
    }

    public function put($uri, $payload, $headers)
    {
        return $this->request('PUT', $uri, $payload, $headers);
    }

    public function post($payload, $headers)
    {
        return $this->request('POST', '', $payload, $headers);
    }

    public function delete($uri, $payload, $headers)
    {
        return $this->request('DELETE', $uri, $payload, $headers);
    }

    public function request($method = 'GET', $uri = '', $payload = [], $headers = [])
    {

        if (is_array($uri)) {
            $headers = $payload;
            $payload = $uri;
            $uri = '';
        }

        $uri = $this->endpoint.'/'.$uri;

        return $this->sparkpost->request($method, $uri, $payload, $headers);
    }
}