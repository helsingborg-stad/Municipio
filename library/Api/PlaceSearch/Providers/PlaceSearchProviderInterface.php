<?php

namespace Municipio\Api\PlaceSearch\Providers;

/**
 * Interface PlaceSearchProviderInterface
 *
 * @package Municipio\Api\PlaceSearch\Providers
 */
interface PlaceSearchProviderInterface
{
    /**
     * Search for a place using the provider's API.
     *
     * @param array $args The search arguments.
     *
     * @return array An array of place data.
     */
    public function search(array $args = []): array;
}
