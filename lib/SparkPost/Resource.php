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

    public function get($uri, $payload, $header)
    {
        return $this->sparkpost->request('GET', $this->endpoint.'/'.$uri, $payload, $header);
    }

    public function post($payload, $header)
    {
        return $this->sparkpost->request('POST', $this->endpoint, $payload, $header);
    }
}