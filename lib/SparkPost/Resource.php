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

    public function get($uri, $payload)
    {
        return $this->sparkpost->request('GET', $this->endpoint.'/'.$uri, $payload);
    }

    public function post($payload)
    {
        echo $payload;
        return $this->sparkpost->request('POST', $endpoint, $payload);
    }
}
