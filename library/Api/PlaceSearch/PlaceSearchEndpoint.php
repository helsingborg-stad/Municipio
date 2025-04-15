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
                    'required'    => true,
                ],
                'q'        => [
                    'description' => $this->wpService->__('The query to search for.', 'municipio'),
                    'type'        => 'string',
                    'required'    => true,
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
        $providerSlug = $request->get_param('provider');

        $provider = $this->resolveProvider($providerSlug);
        $result   = $provider->search($request->get_param('q'), $request->get_query_params());

        return new WP_REST_Response($result, 200);
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
        $class = '\\Municipio\Api\PlaceSearch\Providers\\' . ucfirst($providerSlug);

        if (class_exists($class)) {
            return new $class($this->wpService);
        }

        return new \Municipio\Api\PlaceSearch\Providers\Openstreetmap($this->wpService);
    }
}
