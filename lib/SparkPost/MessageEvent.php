<?php
namespace SparkPost;


class MessageEvent extends APIResource {
	public $endpoint = 'message-events';

	public function search(Array $queryParams) {
		return $this->get(null, $queryParams);
	}
}