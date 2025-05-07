<?php

namespace Municipio\Api\PlaceSearch;

use Municipio\Api\PlaceSearch\Providers\PlaceSearchProviderInterface;
use Municipio\Api\RestApiEndpoint;
use WP_REST_Request;
use WP_REST_Response;
use WpService\WpService;

/**
 * Class PlaceSearchEndpoint
 *
 * Place Search Endpoint for handling PDF generation based on post IDs.
 *
 * @package Municipio\Api\PlaceSearch
 */
class PlaceSearchEndpoint extends RestApiEndpoint
{
    private const NAMESPACE = 'placesearch/v1';
    private const ROUTE     = '/(?P<provider>[a-zA-Z0-9_-]+)';

    /**
     * PlaceSearchEndpoint constructor.
     *
     * @param WpService $wpService The WpService instance.
     */
    public function __construct(private WpService $wpService)
    {
    }

    /**
     * Handles the registration of the REST route.
     *
     * @return bool Whether the REST route registration was successful.
     */
    public function handleRegisterRestRoute(): bool
    {
        return $this->wpService->registerRestRoute(self::NAMESPACE, self::ROUTE, array(
            'methods'             => 'GET',
            'callback'            => array($this, 'handleRequest'),
            'permission_callback' => '__return_true',
            'args'                => [
                'provider' => [
                    'description' => $this->wpService->__('The provider name.', 'municipio'),
                    'type'        => 'string',
                    'required'    => false,
                ],
                'q'        => [
                    'description' => $this->wpService->__('The query to search for.', 'municipio'),
                    'type'        => 'string',
                    'required'    => false,
                ],
                'reverse' => [
                    'description' => $this->wpService->__('Whether to perform reverse geocoding.', 'municipio'),
                    'type'        => 'boolean',
                    'required'    => false,
                ],
                'lat'     => [
                    'description' => $this->wpService->__('Latitude for reverse geocoding.', 'municipio'),
                    'type'        => 'number',
                    'required'    => false,
                ],
                'lng'     => [
                    'description' => $this->wpService->__('Longitude for reverse geocoding.', 'municipio'),
                    'type'        => 'number',
                    'required'    => false,
                ],
            ],
        ));
    }

    /**
     * Handles the REST API request.
     *
     * @param WP_REST_Request $request The REST API request object.
     *
     * @return WP_REST_Response The REST API response object.
     */
    public function handleRequest(WP_REST_Request $request): WP_REST_Response
    {
        $isInvalidRequest = $this->checkIsRequestInvalid($request);
        if ($isInvalidRequest) {
            return new WP_REST_Response(array(
                'code'    => 'invalid_request',
                'message' => $isInvalidRequest,
            ), 400);
        }

        $providerSlug = $request->get_param('provider');

        $provider = $this->resolveProvider($providerSlug);
        $result   = $provider->search($request->get_query_params());

        return new WP_REST_Response($result, 200);
    }

    /**
     * Checks if the request is invalid based on the provided parameters.
     *
     * @param WP_REST_Request $request The REST API request object.
     *
     * @return false|string False if valid, error message if invalid.
     */
    private function checkIsRequestInvalid(WP_REST_Request $request): false|string
    {
        $args = $request->get_query_params();
        $hasQ = !empty($args['q']);
        $hasReverse = !empty($args['reverse']);
        $hasCoordinates = $hasReverse && !empty($args['lat']) && !empty($args['lng']);

        if (!$hasQ && !$hasReverse) {
            return $this->wpService->__('Missing q or reverse parameter', 'municipio');
        }

        if ($hasReverse && !$hasCoordinates) {
            return $this->wpService->__('Missing lat/lng for reverse geocoding', 'municipio');
        }

        return false;
    }

    /**
     * Resolves the provider based on the slug.
     *
     * @param string         $providerSlug The provider slug.
     * @param WP_REST_Request $request      The REST API request object.
     *
     * @return PlaceSearchProviderInterface The resolved provider instance.
     */
    private function resolveProvider(string $providerSlug): PlaceSearchProviderInterface
    {
        switch ($providerSlug) {
            case 'openstreetmap':
            default:
                return new \Municipio\Api\PlaceSearch\Providers\Openstreetmap($this->wpService);
        }
    }
}
