<?php

namespace SparkPost;

use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\ResponseInterface as ResponseInterface;
use Psr\Http\Message\StreamInterface as StreamInterface;

class SparkPostResponse implements ResponseInterface
{
    /**
     * ResponseInterface to be wrapped by SparkPostResponse.
     */
    private $response;

    /**
     * Array with the request values sent.
     */
    private $request;

    /**
     * set the response to be wrapped.
     *
     * @param ResponseInterface $response
     */
    public function __construct(ResponseInterface $response, $request = null)
    {
        $this->response = $response;
        $this->request = $request;
    }

    /**
     * Returns the request values sent.
     *
     * @return array $request
     */
    public function getRequest()
    {
        return $this->request;
    }

    public function getBody() : StreamInterface
    {
        return $this->response->getBody();
    }

    /**
     * pass these down to the response given in the constructor.
     */
    public function getProtocolVersion() : string
    {
        return $this->response->getProtocolVersion();
    }

    public function withProtocolVersion($version) : MessageInterface
    {
        return $this->response->withProtocolVersion($version);
    }

    public function getHeaders() : array
    {
        return $this->response->getHeaders();
    }

    public function hasHeader($name) : bool
    {
        return $this->response->hasHeader($name);
    }

    public function getHeader($name) : array
    {
        return $this->response->getHeader($name);
    }

    public function getHeaderLine($name) : string
    {
        return $this->response->getHeaderLine($name);
    }

    public function withHeader($name, $value) : MessageInterface
    {
        return $this->response->withHeader($name, $value);
    }

    public function withAddedHeader($name, $value) : MessageInterface
    {
        return $this->response->withAddedHeader($name, $value);
    }

    public function withoutHeader($name) : MessageInterface
    {
        return $this->response->withoutHeader($name);
    }

    public function withBody(StreamInterface $body) : MessageInterface
    {
        return $this->response->withBody($body);
    }

    public function getStatusCode() : int
    {
        return $this->response->getStatusCode();
    }

    public function withStatus($code, $reasonPhrase = '') : ResponseInterface
    {
        return $this->response->withStatus($code, $reasonPhrase);
    }

    public function getReasonPhrase() : string
    {
        return $this->response->getReasonPhrase();
    }
}
