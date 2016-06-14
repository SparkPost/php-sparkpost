<?php

namespace SparkPost;

use Http\Client\Exception\HttpException as HttpException;

class SparkPostException extends \Exception {

    /**
     * Variable to hold json decoded body from http response
     */
    private $body = null;

    /**
     * Sets up the custom exception and copies over original exception values
     *
     * @param Exception $exception - the exception to be wrapped
     */
    public function __construct(\Exception $exception) {
        $message = $exception->getMessage();
        if($exception instanceof HttpException) {
            $message = $exception->getResponse()->getBody()->__toString();
            $this->body = json_decode($message, true);
        }

        parent::__construct($message, $exception->getCode(), $exception->getPrevious());
    }


    /**
     * Returns the body
     *
     * @return array $body - the json decoded body from the http response
     */
    public function getBody() {
        return $this->body;
    }
}

?>