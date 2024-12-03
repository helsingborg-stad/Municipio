<?php

namespace Municipio\Api\Posts;

use WP_REST_Request;

interface MunicipioPostsResolverInterface
{
    public function resolve(WP_REST_Request $request): ?array;
    public function canResolveRequest(WP_REST_Request $request): bool;
    public function getViewPaths(): array;
    public function getIdentifier(): string;
}