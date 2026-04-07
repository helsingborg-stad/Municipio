<?php

declare(strict_types=1);

namespace Municipio\Api\Customize;

use Municipio\Api\Customize\Config\CustomizeConfig;
use Municipio\Api\Customize\Config\CustomizeConfigInterface;
use Municipio\Api\RestApiEndpoint;
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
    private const CHANGESET_POST_TYPE = 'customize_changeset';
    private const CHANGESET_QUERY_VAR = 'customize_changeset_uuid';

    private readonly CustomizeConfigInterface $config;

    public function __construct(
        private readonly RegisterRestRoute&CurrentUserCan&SetThemeMod&UpdatePostMeta&WpSavePostRevision&GetPosts&GetQueryVar&IsCustomizePreview&ApplyFilters $wpService,
        ?CustomizeConfigInterface $config = null,
    ) {
        $this->config = $config ?? new CustomizeConfig($this->wpService);
    }

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

        $changesetId = $this->resolveChangesetId($request);
        if ($changesetId !== null) {
            $didSave = $this->wpService->updatePostMeta(
                $changesetId,
                $this->config->getThemeModKey(),
                $encodedTokens,
            );

            if ($didSave !== false) {
                $this->wpService->wpSavePostRevision($changesetId);
            }
        } else {
            $didSave = $this->wpService->setThemeMod($this->config->getThemeModKey(), $encodedTokens);
        }

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

    private function resolveChangesetId(WP_REST_Request $request): ?int
    {
        $changesetUuid = $request->get_param(self::CHANGESET_QUERY_VAR);
        if ((!is_string($changesetUuid) || empty($changesetUuid)) && !$this->wpService->isCustomizePreview()) {
            return null;
        }

        if (!is_string($changesetUuid) || empty($changesetUuid)) {
            $changesetUuid = $this->wpService->getQueryVar(self::CHANGESET_QUERY_VAR, '');
        }

        if (!is_string($changesetUuid) || empty($changesetUuid)) {
            return null;
        }

        $changesets = $this->wpService->getPosts([
            'post_type' => self::CHANGESET_POST_TYPE,
            'name' => $changesetUuid,
            'post_status' => 'any',
            'numberposts' => 1,
            'fields' => 'ids',
        ]);

        if (!isset($changesets[0]) || !is_numeric($changesets[0])) {
            return null;
        }

        return (int) $changesets[0];
    }
}
