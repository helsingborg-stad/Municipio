<?php

declare(strict_types=1);

namespace Municipio\Styleguide\Customize\RestApi;

use Municipio\Styleguide\Customize\RestApi\Config\CustomizeConfigInterface;
use Municipio\Styleguide\Customize\RestApi\Support\CustomizeTokensReaderInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use WpService\Contracts\__;
use WpService\Contracts\ApplyFilters;
use WpService\Contracts\CurrentUserCan;
use WpService\Contracts\GetPostMeta;
use WpService\Contracts\GetPosts;
use WpService\Contracts\GetQueryVar;
use WpService\Contracts\GetThemeMod;
use WpService\Contracts\IsCustomizePreview;
use WpService\Contracts\RegisterRestRoute;

class GetTest extends TestCase
{
    public function testCanBeInstantiated(): void
    {
        $endpoint = new Get($this->getWpServiceMock(true), $this->createCustomizeConfig(), $this->createTokensReader());

        $this->assertInstanceOf(Get::class, $endpoint);
    }

    public function testHandleRegisterRestRouteReturnsTrue(): void
    {
        $endpoint = new Get($this->getWpServiceMock(true), $this->createCustomizeConfig(), $this->createTokensReader());

        $this->assertTrue($endpoint->handleRegisterRestRoute());
    }

    public function testPermissionCallbackReturnsCurrentUserCapability(): void
    {
        $endpointWithAccess = new Get($this->getWpServiceMock(true, true), $this->createCustomizeConfig(), $this->createTokensReader());
        $endpointWithoutAccess = new Get($this->getWpServiceMock(true, false), $this->createCustomizeConfig(), $this->createTokensReader());

        $this->assertTrue($endpointWithAccess->permissionCallback());
        $this->assertFalse($endpointWithoutAccess->permissionCallback());
    }

    public function testHandleRequestReturnsDecodedJsonWhenFileContainsValidJson(): void
    {
        $request = new \WP_REST_Request('GET');
        $tokensReader = $this->createTokensReader('{"color":{"primary":"#005ea5"}}');
        $tokensReader
            ->expects($this->once())
            ->method('read')
            ->with($this->identicalTo($request))
            ->willReturn('{"color":{"primary":"#005ea5"}}');
        $endpoint = new Get($this->getWpServiceMock(true), $this->createCustomizeConfig(), $tokensReader);

        $response = $endpoint->handleRequest($request);

        $this->assertInstanceOf(\WP_REST_Response::class, $response);
    }

    public function testHandleRequestReturnsErrorWhenJsonIsInvalid(): void
    {
        $request = new \WP_REST_Request('GET');
        $tokensReader = $this->createTokensReader('{"invalid"');
        $tokensReader
            ->expects($this->once())
            ->method('read')
            ->with($this->identicalTo($request))
            ->willReturn('{"invalid"');
        $endpoint = new Get($this->getWpServiceMock(true), $this->createCustomizeConfig(), $tokensReader);

        $response = $endpoint->handleRequest($request);

        $this->assertInstanceOf(\WP_Error::class, $response);
    }

    public function testHandleRequestReturnsEmptyArrayWhenNoDataIsAvailable(): void
    {
        $request = new \WP_REST_Request('GET');
        $tokensReader = $this->createTokensReader(null);
        $tokensReader
            ->expects($this->once())
            ->method('read')
            ->with($this->identicalTo($request))
            ->willReturn(null);
        $endpoint = new Get($this->getWpServiceMock(true), $this->createCustomizeConfig(), $tokensReader);

        $response = $endpoint->handleRequest($request);

        $this->assertInstanceOf(\WP_REST_Response::class, $response);
    }

    public function testHandleRequestReadsFromChangesetMeta(): void
    {
        $wpService = $this->getWpServiceMock(true, true, [999], true, '');
        $request = $this->createMock(\WP_REST_Request::class);
        $tokensReader = $this->createTokensReader('{"color":{"primary":"#005ea5"}}');
        $tokensReader
            ->expects($this->once())
            ->method('read')
            ->with($this->identicalTo($request))
            ->willReturn('{"color":{"primary":"#005ea5"}}');
        $endpoint = new Get($wpService, $this->createCustomizeConfig(), $tokensReader);

        $response = $endpoint->handleRequest($request);

        $this->assertInstanceOf(\WP_REST_Response::class, $response);
    }

    private function createCustomizeConfig(): CustomizeConfigInterface
    {
        $config = $this->createMock(CustomizeConfigInterface::class);
        $config->method('getGetPermissionCapability')->willReturn('edit_theme_options');

        return $config;
    }

    private function createTokensReader(?string $json = null): CustomizeTokensReaderInterface|MockObject
    {
        $reader = $this->createMock(CustomizeTokensReaderInterface::class);
        $reader->method('read')->willReturn($json);

        return $reader;
    }

    private function getWpServiceMock(
        bool $registerRestRoute = true,
        bool $currentUserCan = true,
        array $getPostsResponse = [],
        bool $isCustomizePreview = false,
        string $changesetUuidQueryVar = '',
    ): RegisterRestRoute&CurrentUserCan&GetThemeMod&GetPostMeta&GetPosts&GetQueryVar&IsCustomizePreview&ApplyFilters&__ {
        return new class(
            $registerRestRoute,
            $currentUserCan,
            $getPostsResponse,
            $isCustomizePreview,
            $changesetUuidQueryVar,
        ) implements RegisterRestRoute, CurrentUserCan, GetThemeMod, GetPostMeta, GetPosts, GetQueryVar, IsCustomizePreview, ApplyFilters, __ {
            public function __construct(
                private bool $registerRestRoute,
                private bool $currentUserCan,
                private array $getPostsResponse,
                private bool $isCustomizePreview,
                private string $changesetUuidQueryVar,
            ) {}

            public function registerRestRoute(string $namespace, string $route, array $args = [], bool $override = false): bool
            {
                return $this->registerRestRoute;
            }

            public function currentUserCan(string $capability, mixed ...$args): bool
            {
                return $this->currentUserCan;
            }

            public function getThemeMod(string $name, mixed $defaultValue = false): mixed
            {
                return $defaultValue;
            }

            public function getPostMeta(int $postId, string $key = '', bool $single = false): mixed
            {
                return '{"color":{"primary":"#005ea5"}}';
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

            public function __(string $text, string $domain = 'default'): string
            {
                return $text;
            }
        };
    }
}
