<?php

namespace SparkPost;

/**
 * SDK class for querying the Suppression Lists API.
 *
 * @see https://developers.sparkpost.com/api/#/reference/suppression-list
 */
class SuppressionHandler extends APIResource
{
    /**
     * @var string
     */
    public $endpoint = 'suppression-list';

    /**
     * Method for issuing search requests to the Suppression List API.
     *
     * The method passes-through all of the query parameters - the valid ones are listed at https://developers.sparkpost.com/api/#/reference/suppression-list
     *
     *
     * @param array $queryParams The query parameters.
     *
     * @return array The result of the query.
     */
    public function search(array $queryParams)
    {
        return $this->get(null, $queryParams);
    }

    /**
     * Method for adding users the Suppression List.
     *
     * The method passes-through all of the query parameters - the valid ones are listed at https://developers.sparkpost.com/api/#/reference/suppression-list
     *
     *
     * @param array $queryParams The query parameters.
     *
     * @return array The result of the query.
     */
    public function insert(array $queryParams)
    {
        return $this->update(null, $queryParams);
    }

    /**
     * Method for deleting an email address from the Suppression List.
     *
     *
     * @param array $queryParams The query parameters.
     *
     * @return array The result of the query.
     */
    public function deleteAddress($email)
    {
        return $this->delete($email);
    }

}
