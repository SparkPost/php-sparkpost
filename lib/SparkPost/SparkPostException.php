<?php

namespace SparkPost;

use Http\Client\Exception\HttpException as HttpException;

class SparkPostException extends \Exception {

    private $body = null;

    public function __construct(\Exception $exception) {
        $message = $exception->getMessage();
        if($exception instanceof HttpException) {
            $message = $exception->getResponse()->getBody()->__toString();
            $this->body = json_decode($message, true);
        }

        parent::__construct($message, $exception->getCode(), $exception->getPrevious());
    }

    public function getBody() {
        return $this->body;
    }
}

?>