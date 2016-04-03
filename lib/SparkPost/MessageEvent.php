<?php
namespace SparkPost;

/**
 * SDK class for querying message events API
 * @package SparkPost
 */
class MessageEvent extends APIResource
{
  public $endpoint = 'message-events';

  /**
   * Method for issuing search requests to the Message Events API.
   *
   * The method passes-through all of the query parameters listed at
   * @link https://developers.sparkpost.com/api/#/reference/message-events/events-documentation/search-for-message-events
   *
   * @param array $queryParams  The query parameters.  Note that a query parameter containing an array
   * is collapsed into a comma-separated list.
   *
   * @return array The result of the query.
   */
  public function search(Array $queryParams)
  {
    return $this->get(null, $queryParams);
  }
}