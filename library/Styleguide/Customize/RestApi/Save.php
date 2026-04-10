<?php

declare(strict_types=1);

namespace Municipio\Styleguide\Customize\RestApi;

use Municipio\Api\Customize\Config\CustomizeConfigInterface;
use Municipio\Api\Customize\Support\CustomizeTokensWriterInterface;
use Municipio\Api\RestApiEndpoint;
use Municipio\Styleguide\Customize\RestApi\Config\CustomizeConfigInterface as ConfigCustomizeConfigInterface;
use Municipio\Styleguide\Customize\RestApi\Support\CustomizeTokensWriterInterface as SupportCustomizeTokensWriterInterface;
use WP_Error;
use WP_Http;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;
use WpService\Contracts\ApplyFilters;
use WpService\Contracts\CurrentUserCan;
use WpService\Contracts\GetPosts;
use WpService\Contracts\GetQueryVar;
use WpService\Contracts\IsCustomizePreview;
use WpService\Contracts\RegisterRestRoute;
use WpService\Contracts\SetThemeMod;
use WpService\Contracts\UpdatePostMeta;
use WpService\Contracts\WpSavePostRevision;

/**
 * REST endpoint for saving styleguide design token customizations.
 */
class Save extends RestApiEndpoint
{
    private const NAMESPACE = 'municipio/v1';
    private const ROUTE = 'customize/design';

    public function __construct(
        private readonly RegisterRestRoute&CurrentUserCan&SetThemeMod&UpdatePostMeta&WpSavePostRevision&GetPosts&GetQueryVar&IsCustomizePreview&ApplyFilters $wpService,
        private readonly ConfigCustomizeConfigInterface $config,
        private readonly SupportCustomizeTokensWriterInterface $tokensWriter,
    ) {}

    /**
     * Registers the REST route.
     *
     * @return bool True when route registration succeeded.
     */
    public function handleRegisterRestRoute(): bool
    {
        return $this->wpService->registerRestRoute(self::NAMESPACE, self::ROUTE, [
            'methods' => WP_REST_Server::CREATABLE,
            'callback' => [$this, 'handleRequest'],
            'permission_callback' => [$this, 'permissionCallback'],
        ]);
    }

    /**
     * Handles the REST request and persists token customization JSON.
     *
     * Accepts either { "tokens": { ... } } or a direct JSON object payload.
     *
     * @param WP_REST_Request $request The request object.
     *
     * @return WP_REST_Response|WP_Error
     */
    public function handleRequest(WP_REST_Request $request): WP_REST_Response|WP_Error
    {
        $params = $request->get_json_params();
        if (!is_array($params)) {
            return $this->createBadRequestError('Payload must be a JSON object.');
        }

        $tokens = $params['tokens'] ?? $params;
        if (!is_array($tokens)) {
            return $this->createBadRequestError('Tokens must be a JSON object.');
        }

        $encodedTokens = json_encode($tokens);
        if ($encodedTokens === false) {
            $error = new WP_Error(
                'unable_to_encode_customized_design_tokens',
                'Unable to encode token customization data.',
                ['status' => WP_Http::INTERNAL_SERVER_ERROR],
            );

            return $error;
        }

        $didSave = $this->tokensWriter->write($request, $encodedTokens);

        if (!$didSave) {
            $error = new WP_Error(
                'unable_to_save_customized_design_tokens',
                'Unable to save token customization data.',
                ['status' => WP_Http::INTERNAL_SERVER_ERROR],
            );

            return $error;
        }

        return new WP_REST_Response($tokens, WP_Http::OK);
    }

    /**
     * Permission callback for design token endpoint.
     *
     * @return bool True when user can edit theme options.
     */
    public function permissionCallback(): bool
    {
        return $this->wpService->currentUserCan($this->config->getSavePermissionCapability());
    }

    private function createBadRequestError(string $message): WP_Error
    {
        return new WP_Error(
            'invalid_customized_design_tokens_payload',
            $message,
            ['status' => WP_Http::BAD_REQUEST],
        );
    }
}
