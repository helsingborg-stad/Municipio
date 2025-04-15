<?php

namespace Municipio\Api\PlaceSearch\Providers;

use WpService\Contracts\IsWpError;
use WpService\Contracts\WpRemoteGet;
use WpService\Contracts\WpRemoteRetrieveBody;
use Municipio\Schema\Schema;

/**
 * Class Openstreetmap
 *
 * @package Municipio\Api\PlaceSearch\Providers
 */
class Openstreetmap implements PlaceSearchProviderInterface
{
    /**
     * Openstreetmap constructor.
     *
     * @param WpRemoteGet&IsWpError&WpRemoteRetrieveBody $wpService
     */
    public function __construct(private WpRemoteGet&IsWpError&WpRemoteRetrieveBody $wpService)
    {
    }

    /**
     * Search for a place using OpenStreetMap Nominatim API.
     *
     * @param string $query The search query.
     * @param array  $args  Additional arguments (not used).
     *
     * @return array An array of place data.
     */
    public function search(string $query, array $args = []): array
    {
        $url = 'https://nominatim.openstreetmap.org/search?q=' . urlencode($query) . '&format=json';

        $response = $this->wpService->wpRemoteGet($url);

        if ($this->wpService->isWpError($response)) {
            return [];
        }

        $response = json_decode($this->wpService->wpRemoteRetrieveBody($response), true);

        $data = $this->transformResponseToSchema($response ?: []);

        return $data;
    }

    /**
     * Transform the OpenStreetMap response to a schema format.
     *
     * @param array $response The OpenStreetMap response.
     *
     * @return array An array of transformed schema data.
     */
    private function transformResponseToSchema(array $response): array
    {
        $schemaTransformedItems = [];
        foreach ($response as $value) {
            $schema = Schema::place();
            $schema->latitude($value['lat']);
            $schema->longitude($value['lon']);
            $schema->address($value['display_name']);

            $schemaTransformedItems[] = $schema->toArray();
        }

        return $schemaTransformedItems;
    }
}
