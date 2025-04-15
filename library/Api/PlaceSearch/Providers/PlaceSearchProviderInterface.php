<?php

namespace Municipio\Api\PlaceSearch\Providers;


/**
 * Interface PlaceSearchProviderInterface
 *
 * @package Municipio\Api\PlaceSearch\Providers
 */
interface PlaceSearchProviderInterface
{
    public function search(array $args = []): array;
}
