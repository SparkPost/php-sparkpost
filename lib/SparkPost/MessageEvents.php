<?php

namespace SparkPost;

/**
 * SDK class for querying the Message Events API.
 *
 * @see https://developers.sparkpost.com/api/#/reference/message-events
 */
class MessageEvents extends APIResource
{
    /**
     * @var string
     */
    public $endpoint = 'message-events';

    /**
     * Method for issuing search requests to the Message Events API.
     *
     * The method passes-through all of the query parameters - the valid ones are listed at
     *
     * @link https://developers.sparkpost.com/api/#/reference/message-events/events-documentation/search-for-message-events
     *
     * @param array $queryParams The query parameters.  Note that a query parameter containing an array
     *                           is collapsed into a comma-separated list.
     *
     * @return array The result of the query.
     */
    public function search(array $queryParams)
    {
        // check for DateTime objects & replace them with the formatted string equivalent
        foreach (['from', 'to'] as $dateTimeParam) {
            if (isset($queryParams[$dateTimeParam]) && $queryParams[$dateTimeParam] instanceof \DateTime) {
                // the message events API doesn't allow the seconds or GMT offset, so strip them
                $queryParams[$dateTimeParam] = substr($queryParams[$dateTimeParam]->format(\DateTime::ATOM), 0, 16);
            }
        }

        return $this->get(null, $queryParams);
    }

    /**
     * List descriptions of the event fields that could be included in a response from the MessageEvent::search() method.
     *
     * @return array The event field descriptions.
     */
    public function documentation()
    {
        return $this->get('events/documentation');
    }

    /**
     * List examples of the event data that will be included in a response from the MessageEvent::search() method.
     *
     * @param array $events (optional) Event types for which to get a sample payload.  If not provided, samples
     *                      for all events will be returned.
     *
     * @return array Sample events.
     */
    public function samples(array $events = [])
    {
        return $this->get('events/samples', ['events' => $events]);
    }
}
