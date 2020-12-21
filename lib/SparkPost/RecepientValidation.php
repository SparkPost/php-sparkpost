<?php


namespace SparkPost;

class RecepientValidation extends ResourceBase {

    function __construct(SparkPost $sparkpost) {

        parent::__construct($sparkpost, 'recipient-validation');
    }

    /**
     * Send get request to recepient-validation endpoint
     *
     * @return SparkPostPromise or SparkPostResponse depending on sync or async request
     */
    public function get($uri = '', $payload = [], $headers = [])
    {

        return parent::get($uri, $payload, $headers);
    }

}
