<?php

namespace Municipio\Api\PlaceSearch\Providers;

interface PlaceSearchProviderInterface {
    public function search(string $query, array $args = []): array;
}