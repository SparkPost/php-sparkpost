<?php

namespace SparkPost;

use Http\Promise\Promise as HttpPromise;

class SparkPostPromise implements HttpPromise
{
	private $promise;

	public function __construct(HttpPromise $promise) {
		$this->promise = $promise;
	}

	public function then(callable $onFulfilled = null, callable $onRejected = null) {
		$this->promise->then(
		function($response) {
			$onFulfilled(new SparkPostResponse($response));
		},
		function(\Exception $exception) {
			$onRejected(new SparkPostException($exception));
		});
	}

	public function getState() {
		return $this->promise->getState();
	}


	public function wait($unwrap = true) {
		try
		{
		    $response = $this->promise->wait($unwrap);
		    return new SparkPostResponse($response);
		}
		catch (\Exception $exception)
		{
		   throw new SparkPostException($exception);
		}
	}
}

?>