<?php

declare(strict_types=1);

namespace Municipio\Api\Customize;

use PHPUnit\Framework\TestCase;
use WpService\Contracts\__;
use WpService\Contracts\ApplyFilters;
use WpService\Contracts\CurrentUserCan;
use WpService\Contracts\GetThemeMod;
use WpService\Contracts\RegisterRestRoute;

class GetTest extends TestCase
{
    public function testCanBeInstantiated(): void
    {
        $endpoint = new Get($this->getWpServiceMock(true));

        $this->assertInstanceOf(Get::class, $endpoint);
    }

    public function testHandleRegisterRestRouteReturnsTrue(): void
    {
        $endpoint = new Get($this->getWpServiceMock(true));

        $this->assertTrue($endpoint->handleRegisterRestRoute());
    }

    public function testPermissionCallbackReturnsCurrentUserCapability(): void
    {
        $endpointWithAccess = new Get($this->getWpServiceMock(true, true));
        $endpointWithoutAccess = new Get($this->getWpServiceMock(true, false));

        $this->assertTrue($endpointWithAccess->permissionCallback());
        $this->assertFalse($endpointWithoutAccess->permissionCallback());
    }

    public function testHandleRequestReturnsDecodedJsonWhenFileContainsValidJson(): void
    {
        $endpoint = new class($this->getWpServiceMock(true)) extends Get {
            protected function readCustomizedDesignTokens(): ?string
            {
                return '{"color":{"primary":"#005ea5"}}';
            }
        };

        $response = $endpoint->handleRequest(new \WP_REST_Request('GET'));

        $this->assertInstanceOf(\WP_REST_Response::class, $response);
    }

    public function testHandleRequestReturnsErrorWhenJsonIsInvalid(): void
    {
        $endpoint = new class($this->getWpServiceMock(true)) extends Get {
            protected function readCustomizedDesignTokens(): ?string
            {
                return '{"invalid"';
            }
        };

        $response = $endpoint->handleRequest(new \WP_REST_Request('GET'));

        $this->assertInstanceOf(\WP_Error::class, $response);
    }

    public function testHandleRequestReturnsEmptyArrayWhenNoDataIsAvailable(): void
    {
        $endpoint = new class($this->getWpServiceMock(true)) extends Get {
            protected function readCustomizedDesignTokens(): ?string
            {
                return null;
            }
        };

        $response = $endpoint->handleRequest(new \WP_REST_Request('GET'));

        $this->assertInstanceOf(\WP_REST_Response::class, $response);
    }

    private function getWpServiceMock(bool $registerRestRoute = true, bool $currentUserCan = true): RegisterRestRoute&CurrentUserCan&GetThemeMod&ApplyFilters&__
    {
        return new class($registerRestRoute, $currentUserCan) implements RegisterRestRoute, CurrentUserCan, GetThemeMod, ApplyFilters, __ {
            public function __construct(
                private bool $registerRestRoute,
                private bool $currentUserCan,
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
