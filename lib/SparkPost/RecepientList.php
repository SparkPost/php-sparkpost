<?php

namespace SparkPost;

class RecepientList extends ResourceBase {

    private $id = 0;
    private $name = '';
    private $description = '';
    private $recepients = [];

    function __construct(SparkPost $sparkpost) {

        parent::__construct($sparkpost, 'recipient-lists');
    }

    /**
     * Send get request to recepient-list endpoint
     *
     * @return SparkPostPromise or SparkPostResponse depending on sync or async request
     */
    public function get($uri = '', $payload = [], $headers = [])
    {

        return parent::get($uri, $payload, $headers);
    }

}
