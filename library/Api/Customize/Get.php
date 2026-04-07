<?php

declare(strict_types=1);

namespace Municipio\Api\Customize;

use Municipio\Api\Customize\Config\CustomizeConfig;
use Municipio\Api\Customize\Config\CustomizeConfigInterface;
use Municipio\Api\Customize\Support\ChangesetIdResolver;
use Municipio\Api\Customize\Support\CustomizeTokensReader;
use Municipio\Api\Customize\Support\CustomizeTokensReaderInterface;
use Municipio\Api\RestApiEndpoint;
use WP_Error;
use WP_Http;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;
use WpService\Contracts\__;
use WpService\Contracts\ApplyFilters;
use WpService\Contracts\CurrentUserCan;
use WpService\Contracts\GetPostMeta;
use WpService\Contracts\GetPosts;
use WpService\Contracts\GetQueryVar;
use WpService\Contracts\GetThemeMod;
use WpService\Contracts\IsCustomizePreview;
use WpService\Contracts\RegisterRestRoute;

/**
 * REST endpoint for retrieving styleguide design token customizations.
 */
class Get extends RestApiEndpoint
{
    private const NAMESPACE = 'municipio/v1';
    private const ROUTE = 'customize/design';

    private readonly CustomizeConfigInterface $config;
    private readonly CustomizeTokensReaderInterface $tokensReader;

    public function __construct(
        private readonly RegisterRestRoute&CurrentUserCan&GetThemeMod&GetPostMeta&GetPosts&GetQueryVar&IsCustomizePreview&ApplyFilters&__ $wpService,
        ?CustomizeConfigInterface $config = null,
        ?CustomizeTokensReaderInterface $tokensReader = null,
    ) {
        $this->config = $config ?? new CustomizeConfig($this->wpService);
        $this->tokensReader = $tokensReader ?? new CustomizeTokensReader(
            $this->wpService,
            $this->config,
            new ChangesetIdResolver($this->wpService),
        );
    }

    /**
     * Registers the REST route.
     *
     * @return bool True when route registration succeeded.
     */
    public function handleRegisterRestRoute(): bool
    {
        return $this->wpService->registerRestRoute(self::NAMESPACE, self::ROUTE, [
            'methods' => WP_REST_Server::READABLE,
            'callback' => [$this, 'handleRequest'],
            'permission_callback' => [$this, 'permissionCallback'],
        ]);
    }

    /**
     * Handles the REST request and returns decoded token customization data.
     *
     * @param WP_REST_Request $request The request object.
     *
     * @return WP_REST_Response|WP_Error
     */
    public function handleRequest(WP_REST_Request $request): WP_REST_Response|WP_Error
    {
        $json = $this->readCustomizedDesignTokens($request);

        if ($json === null) {
            return new WP_REST_Response([], WP_Http::OK);
        }

        $decodedJson = json_decode($json, true);
        if (!is_array($decodedJson)) {
            return new WP_Error(
                'invalid_customized_design_tokens',
                $this->wpService->__('Design token customization data is invalid JSON.', 'municipio'),
                ['status' => WP_Http::INTERNAL_SERVER_ERROR],
            );
        }

        return new WP_REST_Response($decodedJson, WP_Http::OK);
    }

    /**
     * Permission callback for design token endpoint.
     *
     * @return bool True when user can edit theme options.
     */
    public function permissionCallback(): bool
    {
        return $this->wpService->currentUserCan($this->config->getGetPermissionCapability());
    }

    /**
     * Reads design token customization JSON from theme mods.
     *
     * @return string|null JSON string or null when unavailable.
     */
    protected function readCustomizedDesignTokens(WP_REST_Request $request): ?string
    {
        return $this->tokensReader->read($request);
    }
}
