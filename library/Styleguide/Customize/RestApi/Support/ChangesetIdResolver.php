<?php

declare(strict_types=1);

namespace Municipio\Styleguide\Customize\RestApi\Support;

use WP_REST_Request;
use WpService\Contracts\GetPosts;
use WpService\Contracts\GetQueryVar;
use WpService\Contracts\IsCustomizePreview;

class ChangesetIdResolver implements ChangesetIdResolverInterface
{
    private const CHANGESET_POST_TYPE = 'customize_changeset';
    private const CHANGESET_QUERY_VAR = 'customize_changeset_uuid';

    public function __construct(
        private readonly GetPosts&GetQueryVar&IsCustomizePreview $wpService,
    ) {}

    /**
     * Resolve customize_changeset post ID from request/query context.
     */
    public function resolve(WP_REST_Request $request): ?int
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
