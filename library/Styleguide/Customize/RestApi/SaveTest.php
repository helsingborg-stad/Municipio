<?php

declare(strict_types=1);

namespace Municipio\Styleguide\Customize\RestApi;

use Municipio\Styleguide\Customize\RestApi\Config\CustomizeConfigInterface;
use Municipio\Styleguide\Customize\RestApi\Support\CustomizeTokensWriterInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use WpService\Contracts\ApplyFilters;
use WpService\Contracts\CurrentUserCan;
use WpService\Contracts\GetPosts;
use WpService\Contracts\GetQueryVar;
use WpService\Contracts\IsCustomizePreview;
use WpService\Contracts\RegisterRestRoute;
use WpService\Contracts\SetThemeMod;
use WpService\Contracts\UpdatePostMeta;
use WpService\Contracts\WpSavePostRevision;

class SaveTest extends TestCase
{
    public function testCanBeInstantiated(): void
    {
        $endpoint = new Save($this->getWpServiceMock(true, true, true), $this->createCustomizeConfig(), $this->createTokensWriter());

        $this->assertInstanceOf(Save::class, $endpoint);
    }

    public function testHandleRegisterRestRouteReturnsTrue(): void
    {
        $endpoint = new Save($this->getWpServiceMock(true, true, true), $this->createCustomizeConfig(), $this->createTokensWriter());

        $this->assertTrue($endpoint->handleRegisterRestRoute());
    }

    public function testPermissionCallbackReturnsCurrentUserCapability(): void
    {
        $endpointWithAccess = new Save($this->getWpServiceMock(true, true, true), $this->createCustomizeConfig(), $this->createTokensWriter());
        $endpointWithoutAccess = new Save($this->getWpServiceMock(true, false, true), $this->createCustomizeConfig(), $this->createTokensWriter());

        $this->assertTrue($endpointWithAccess->permissionCallback());
        $this->assertFalse($endpointWithoutAccess->permissionCallback());
    }

    public function testHandleRequestSavesTokensFromTokensPayload(): void
    {
        $wpService = $this->getWpServiceMock(true, true, true);
        $request = $this->createMock(\WP_REST_Request::class);
        $request
            ->method('get_json_params')
            ->willReturn([
                'tokens' => ['color' => ['primary' => '#005ea5']],
            ]);
        $tokensWriter = $this->createTokensWriter();
        $tokensWriter
            ->expects($this->once())
            ->method('write')
            ->with($this->identicalTo($request), '{"color":{"primary":"#005ea5"}}')
            ->willReturn(true);

        $endpoint = new Save($wpService, $this->createCustomizeConfig(), $tokensWriter);

        $response = $endpoint->handleRequest($request);

        $this->assertInstanceOf(\WP_REST_Response::class, $response);
    }

    public function testHandleRequestSavesTokensToChangesetMetaWhenChangesetUuidIsProvided(): void
    {
        $wpService = $this->getWpServiceMock(true, true, true, [123]);
        $request = $this->createMock(\WP_REST_Request::class);
        $request
            ->method('get_json_params')
            ->willReturn([
                'tokens' => ['color' => ['primary' => '#005ea5']],
            ]);
        $tokensWriter = $this->createTokensWriter();
        $tokensWriter
            ->expects($this->once())
            ->method('write')
            ->with($this->identicalTo($request), '{"color":{"primary":"#005ea5"}}')
            ->willReturn(true);

        $endpoint = new Save($wpService, $this->createCustomizeConfig(), $tokensWriter);

        $response = $endpoint->handleRequest($request);

        $this->assertInstanceOf(\WP_REST_Response::class, $response);
    }

    public function testHandleRequestReturnsErrorWhenChangesetMetaSaveFails(): void
    {
        $wpService = $this->getWpServiceMock(true, true, true, [123], false);
        $request = $this->createMock(\WP_REST_Request::class);
        $request
            ->method('get_json_params')
            ->willReturn([
                'tokens' => ['color' => ['primary' => '#005ea5']],
            ]);
        $tokensWriter = $this->createTokensWriter(false);

        $endpoint = new Save($wpService, $this->createCustomizeConfig(), $tokensWriter);

        $response = $endpoint->handleRequest($request);

        $this->assertInstanceOf(\WP_Error::class, $response);
    }

    public function testHandleRequestSavesTokensFromDirectObjectPayload(): void
    {
        $wpService = $this->getWpServiceMock(true, true, true);
        $tokensWriter = $this->createTokensWriter();

        $endpoint = new Save($wpService, $this->createCustomizeConfig(), $tokensWriter);

        $request = $this->createMock(\WP_REST_Request::class);
        $request
            ->method('get_json_params')
            ->willReturn([
                'color' => ['primary' => '#005ea5'],
            ]);
        $tokensWriter
            ->expects($this->once())
            ->method('write')
            ->with($this->identicalTo($request), '{"color":{"primary":"#005ea5"}}')
            ->willReturn(true);

        $response = $endpoint->handleRequest($request);

        $this->assertInstanceOf(\WP_REST_Response::class, $response);
    }

    public function testHandleRequestReturnsErrorWhenSaveFails(): void
    {
        $endpoint = new Save($this->getWpServiceMock(true, true, false), $this->createCustomizeConfig(), $this->createTokensWriter(false));

        $request = $this->createMock(\WP_REST_Request::class);
        $request
            ->method('get_json_params')
            ->willReturn([
                'tokens' => ['color' => ['primary' => '#005ea5']],
            ]);

        $response = $endpoint->handleRequest($request);

        $this->assertInstanceOf(\WP_Error::class, $response);
    }

    public function testHandleRequestReturnsErrorWhenPayloadIsNotObject(): void
    {
        $wpService = $this->getWpServiceMock(true, true, true);
        $endpoint = new Save($wpService, $this->createCustomizeConfig(), $this->createTokensWriter());

        $request = $this->createMock(\WP_REST_Request::class);
        $request->method('get_json_params')->willReturn('invalid-payload');

        $response = $endpoint->handleRequest($request);

        $this->assertInstanceOf(\WP_Error::class, $response);
        $this->assertNull($wpService->savedName);
    }

    public function testHandleRequestReturnsErrorWhenTokensIsNotObject(): void
    {
        $wpService = $this->getWpServiceMock(true, true, true);
        $endpoint = new Save($wpService, $this->createCustomizeConfig(), $this->createTokensWriter());

        $request = $this->createMock(\WP_REST_Request::class);
        $request
            ->method('get_json_params')
            ->willReturn([
                'tokens' => 'invalid-tokens',
            ]);

        $response = $endpoint->handleRequest($request);

        $this->assertInstanceOf(\WP_Error::class, $response);
        $this->assertNull($wpService->savedName);
    }

    public function testHandleRequestReturnsErrorWhenJsonEncodeFails(): void
    {
        $wpService = $this->getWpServiceMock(true, true, true);
        $endpoint = new Save($wpService, $this->createCustomizeConfig(), $this->createTokensWriter());

        $request = $this->createMock(\WP_REST_Request::class);
        $request
            ->method('get_json_params')
            ->willReturn([
                'tokens' => ['invalid' => NAN],
            ]);

        $response = $endpoint->handleRequest($request);

        $this->assertInstanceOf(\WP_Error::class, $response);
        $this->assertNull($wpService->savedName);
    }

    private function getWpServiceMock(
        bool $registerRestRoute,
        bool $currentUserCan,
        bool $setThemeMod,
        array $getPostsResponse = [],
        bool $updatePostMeta = true,
        bool $isCustomizePreview = false,
        string $changesetUuidQueryVar = '',
    ): RegisterRestRoute&CurrentUserCan&SetThemeMod&UpdatePostMeta&WpSavePostRevision&GetPosts&GetQueryVar&IsCustomizePreview&ApplyFilters {
        return new class(
            $registerRestRoute,
            $currentUserCan,
            $setThemeMod,
            $getPostsResponse,
            $updatePostMeta,
            $isCustomizePreview,
            $changesetUuidQueryVar,
        ) implements RegisterRestRoute, CurrentUserCan, SetThemeMod, UpdatePostMeta, WpSavePostRevision, GetPosts, GetQueryVar, IsCustomizePreview, ApplyFilters {
            public ?string $savedName = null;
            public mixed $savedValue = null;
            public ?int $updatedPostId = null;
            public ?string $updatedMetaKey = null;
            public mixed $updatedMetaValue = null;
            public ?int $revisionSavedForPostId = null;

            public function __construct(
                private bool $registerRestRoute,
                private bool $currentUserCan,
                private bool $setThemeMod,
                private array $getPostsResponse,
                private bool $updatePostMeta,
                private bool $isCustomizePreview,
                private string $changesetUuidQueryVar,
            ) {}

            public function registerRestRoute(string $routeNamespace, string $route, array $args = [], bool $override = false): bool
            {
                return $this->registerRestRoute;
            }

            public function currentUserCan(string $capability, mixed ...$args): bool
            {
                return $this->currentUserCan;
            }

            public function setThemeMod(string $name, mixed $value): bool
            {
                $this->savedName = $name;
                $this->savedValue = $value;

                return $this->setThemeMod;
            }

            public function updatePostMeta(int $postId, string $metaKey, mixed $metaValue, mixed $prevValue = ''): int|bool
            {
                $this->updatedPostId = $postId;
                $this->updatedMetaKey = $metaKey;
                $this->updatedMetaValue = $metaValue;

                return $this->updatePostMeta;
            }

            public function wpSavePostRevision(int $postId): mixed
            {
                $this->revisionSavedForPostId = $postId;
                return $postId;
            }

            public function getPosts(array $args = null): array
            {
                return $this->getPostsResponse;
            }

            public function getQueryVar(string $queryVar, mixed $defaultValue = ''): mixed
            {
                return $queryVar === 'customize_changeset_uuid' ? $this->changesetUuidQueryVar : $defaultValue;
            }

            public function isCustomizePreview(): bool
            {
                return $this->isCustomizePreview;
            }

            public function applyFilters(string $hookName, mixed $value, mixed ...$args): mixed
            {
                return $value;
            }
        };
    }

    private function createCustomizeConfig(): CustomizeConfigInterface
    {
        $config = $this->createMock(CustomizeConfigInterface::class);
        $config->method('getSavePermissionCapability')->willReturn('edit_theme_options');

        return $config;
    }

    private function createTokensWriter(bool $didSave = true): CustomizeTokensWriterInterface|MockObject
    {
        $writer = $this->createMock(CustomizeTokensWriterInterface::class);
        $writer->method('write')->willReturn($didSave);

        return $writer;
    }
}
