<?php

namespace SparkPost;

use Psr\Http\Message\ResponseInterface as ResponseInterface;
use GuzzleHttp\Psr7\Response as HttpResponse;
use GuzzleHttp\Psr7\MessageTrait;

class Response implements ResponseInterface {

    use MessageTrait;

	private $response;

	public function __construct(HttpResponse $response) {
		$this->response = $response;
	}

    public function getBody()
    {
        $body = $this->response->getBody();
        $body_string = $body->__toString();
        
        if (is_string($body_string)) {
            $json = json_decode($body_string, true);
            
            if (json_last_error() == JSON_ERROR_NONE) {
                return $json;
            }
            else {
                return $body;
            }
        }

        return $body;
    }

	public function getStatusCode()
    {
        return $this->response->getStatusCode();
    }

    public function withStatus($code, $reasonPhrase = '')
    {
        return $this->response->withStatus($code, $reasonPhrase);
    }
    
    public function getReasonPhrase()
    {
        $this->response->getReasonPhrase();
    }

}

?>