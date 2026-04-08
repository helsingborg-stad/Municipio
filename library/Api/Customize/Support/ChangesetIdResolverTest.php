<?php

declare(strict_types=1);

namespace Municipio\Api\Customize\Support;

use PHPUnit\Framework\TestCase;
use WpService\Contracts\GetPosts;
use WpService\Contracts\GetQueryVar;
use WpService\Contracts\IsCustomizePreview;

class ChangesetIdResolverTest extends TestCase
{
    public function testResolveReturnsNullWhenNotInCustomizePreviewAndNoUuidInRequest(): void
    {
        $resolver = new ChangesetIdResolver($this->createWpService([], false, ''));
        $request = $this->createRequestMock(null);

        $this->assertNull($resolver->resolve($request));
    }

    public function testResolveReturnsChangesetIdFromRequestUuid(): void
    {
        $resolver = new ChangesetIdResolver($this->createWpService([123], false, ''));
        $request = $this->createRequestMock('abc-uuid');

        $this->assertSame(123, $resolver->resolve($request));
    }

    public function testResolveReturnsChangesetIdFromQueryVarInPreviewMode(): void
    {
        $resolver = new ChangesetIdResolver($this->createWpService([456], true, 'query-uuid'));
        $request = $this->createRequestMock(null);

        $this->assertSame(456, $resolver->resolve($request));
    }

    public function testResolveReturnsNullWhenMatchingChangesetCannotBeFound(): void
    {
        $resolver = new ChangesetIdResolver($this->createWpService([], true, 'query-uuid'));
        $request = $this->createRequestMock(null);

        $this->assertNull($resolver->resolve($request));
    }

    private function createRequestMock(?string $uuid): \WP_REST_Request
    {
        $request = $this->createMock(\WP_REST_Request::class);
        $request->method('get_param')->with('customize_changeset_uuid')->willReturn($uuid);

        return $request;
    }

    private function createWpService(
        array $changesets,
        bool $isCustomizePreview,
        string $changesetQueryVar,
    ): GetPosts&GetQueryVar&IsCustomizePreview {
        return new class($changesets, $isCustomizePreview, $changesetQueryVar) implements GetPosts, GetQueryVar, IsCustomizePreview {
            public function __construct(
                private array $changesets,
                private bool $isCustomizePreview,
                private string $changesetQueryVar,
            ) {}

            public function getPosts(array $args = null): array
            {
                return $this->changesets;
            }

            public function getQueryVar(string $queryVar, mixed $defaultValue = ''): mixed
            {
                return $queryVar === 'customize_changeset_uuid' ? $this->changesetQueryVar : $defaultValue;
            }

            public function isCustomizePreview(): bool
            {
                return $this->isCustomizePreview;
            }
        };
    }
}
