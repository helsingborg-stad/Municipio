<?php

namespace Municipio\Api\PlaceSearch\Providers;

use WpService\Contracts\IsWpError;
use WpService\Contracts\WpRemoteGet;
use WpService\Contracts\WpRemoteRetrieveBody;
use Municipio\Schema\Schema;
use WP_Error;

/**
 * Class Openstreetmap
 *
 * @package Municipio\Api\PlaceSearch\Providers
 */
class Openstreetmap implements PlaceSearchProviderInterface
{
    /**
     * The OpenStreetMap Nominatim API URL.
     */
    private const API_SEARCH_URL = 'https://nominatim.openstreetmap.org/search';
    private const API_REVERSE_SEARCH_URL  = 'https://nominatim.openstreetmap.org/reverse';

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
    public function search(array $args = []): array
    {
        $data = !empty($args['reverse']) ? $this->fetchReverseSearch($args) : $this->fetchSearch($args);

        if (empty($data)) {
            return [];
        }

        return $data;
    }

    /**
     * Fetch search results from OpenStreetMap Nominatim API.
     *
     * @param array $args The search query and additional arguments.
     *
     * @return array An array of transformed schema data.
     */
    public function fetchSearch(array $args = []): array
    {
        $response = $this->wpService->wpRemoteGet($this->createSearchEndpointUrl($args));

        if ($this->wpService->isWpError($response)) {
            return [];
        }

        $response = json_decode($this->wpService->wpRemoteRetrieveBody($response), true);

        return $this->transformResponseToSchema($response ?: []);
    }

    /**
     * Fetch reverse search results from OpenStreetMap Nominatim API.
     *
     * @param array $args The search query and additional arguments.
     *
     * @return array An array of transformed schema data.
     */
    public function fetchReverseSearch(array $args = []): array
    {
        $response = $this->wpService->wpRemoteGet($this->createReverseSearchEndpointUrl($args));

        if ($this->wpService->isWpError($response)) {
            return [];
        }

        $response = json_decode($this->wpService->wpRemoteRetrieveBody($response), true);

        return $this->transformResponseToSchema($response ? [$response] : [])[0] ?? [];
    }

    /**
     * Create the OpenStreetMap API reverse search endpoint URL.
     *
     * @param array $args The search query and additional arguments.
     *
     * @return string The complete API endpoint URL.
     */
    public function createReverseSearchEndpointUrl(array $args = []): string
    {
        $url = self::API_REVERSE_SEARCH_URL;

        $args = array_merge($args, [
            'format' => 'json',
            'lon'    => $args['lng'],
            'lat'    => $args['lat']
        ]);

        unset($args['lng']);
        unset($args['reverse']);

        return $url . '?' . http_build_query($args);
    }

    /**
     * Create the OpenStreetMap API endpoint URL.
     *
     * @param string $query The search query.
     * @param array  $args  Additional arguments.
     *
     * @return string The complete API endpoint URL.
     */
    public function createSearchEndpointUrl(array $args = []): string
    {
        $url = self::API_SEARCH_URL;

        $args = array_merge($args, [
            'format' => 'json'
        ]);

        return $url . '?' . http_build_query($args);
    }

    /**
     * Transform the OpenStreetMap response to a schema format.
     *
     * @param array $response The OpenStreetMap response.
     *
     * @return array An array of transformed schema data.
     */
    public function transformResponseToSchema(array $response): array
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
