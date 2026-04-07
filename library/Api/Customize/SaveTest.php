<?php

declare(strict_types=1);

namespace Municipio\Api\Customize;

use PHPUnit\Framework\TestCase;
use WpService\Contracts\ApplyFilters;
use WpService\Contracts\CurrentUserCan;
use WpService\Contracts\RegisterRestRoute;
use WpService\Contracts\SetThemeMod;

class SaveTest extends TestCase
{
    public function testCanBeInstantiated(): void
    {
        $endpoint = new Save($this->getWpServiceMock(true, true, true));

        $this->assertInstanceOf(Save::class, $endpoint);
    }

    public function testHandleRegisterRestRouteReturnsTrue(): void
    {
        $endpoint = new Save($this->getWpServiceMock(true, true, true));

        $this->assertTrue($endpoint->handleRegisterRestRoute());
    }

    public function testPermissionCallbackReturnsCurrentUserCapability(): void
    {
        $endpointWithAccess = new Save($this->getWpServiceMock(true, true, true));
        $endpointWithoutAccess = new Save($this->getWpServiceMock(true, false, true));

        $this->assertTrue($endpointWithAccess->permissionCallback());
        $this->assertFalse($endpointWithoutAccess->permissionCallback());
    }

    public function testHandleRequestSavesTokensFromTokensPayload(): void
    {
        $wpService = $this->getWpServiceMock(true, true, true);
        $endpoint = new Save($wpService);

        $request = $this->createMock(\WP_REST_Request::class);
        $request
            ->method('get_json_params')
            ->willReturn([
                'tokens' => ['color' => ['primary' => '#005ea5']],
            ]);

        $response = $endpoint->handleRequest($request);

        $this->assertInstanceOf(\WP_REST_Response::class, $response);
        $this->assertSame('tokens', $wpService->savedName);
        $this->assertSame('{"color":{"primary":"#005ea5"}}', $wpService->savedValue);
    }

    public function testHandleRequestSavesTokensFromDirectObjectPayload(): void
    {
        $wpService = $this->getWpServiceMock(true, true, true);
        $endpoint = new Save($wpService);

        $request = $this->createMock(\WP_REST_Request::class);
        $request
            ->method('get_json_params')
            ->willReturn([
                'color' => ['primary' => '#005ea5'],
            ]);

        $response = $endpoint->handleRequest($request);

        $this->assertInstanceOf(\WP_REST_Response::class, $response);
        $this->assertSame('{"color":{"primary":"#005ea5"}}', $wpService->savedValue);
    }

    public function testHandleRequestReturnsErrorWhenSaveFails(): void
    {
        $endpoint = new Save($this->getWpServiceMock(true, true, false));

        $request = $this->createMock(\WP_REST_Request::class);
        $request
            ->method('get_json_params')
            ->willReturn([
                'tokens' => ['color' => ['primary' => '#005ea5']],
            ]);

        $response = $endpoint->handleRequest($request);

        $this->assertInstanceOf(\WP_Error::class, $response);
    }

    private function getWpServiceMock(bool $registerRestRoute, bool $currentUserCan, bool $setThemeMod): RegisterRestRoute&CurrentUserCan&SetThemeMod&ApplyFilters
    {
        return new class($registerRestRoute, $currentUserCan, $setThemeMod) implements RegisterRestRoute, CurrentUserCan, SetThemeMod, ApplyFilters {
            public ?string $savedName = null;
            public mixed $savedValue = null;

            public function __construct(
                private bool $registerRestRoute,
                private bool $currentUserCan,
                private bool $setThemeMod,
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

            public function applyFilters(string $hookName, mixed $value, mixed ...$args): mixed
            {
                return $value;
            }
        };
    }
}
