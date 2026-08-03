<?php

declare(strict_types=1);

namespace Municipio\Api\Customizer;

use PHPUnit\Framework\TestCase;
use WP_REST_Request;
use WP_REST_Response;

if (!function_exists(__NAMESPACE__ . '\\rest_ensure_response')) {
    /**
     * Test double for the REST response helper.
     *
     * @param mixed $response The response payload.
     *
     * @return WP_REST_Response
     */
    function rest_ensure_response($response)
    {
        return new class ($response) extends WP_REST_Response {
            /**
             * @param mixed $data The response payload.
             */
            public function __construct($data = null)
            {
                $this->data = $data;
                $this->status = 200;
                $this->headers = [];
            }

            /**
             * @return array<string, mixed>
             */
            public function get_headers()
            {
                return $this->headers;
            }

            /**
             * @param string $key The header name.
             * @param mixed $value The header value.
             * @param bool $replace Whether to replace an existing header.
             */
            public function header($key, $value, $replace = true)
            {
                $existingHeader = $this->headers[$key] ?? null;

                if ($replace || $existingHeader === null) {
                    $this->headers[$key] = $value;
                    return;
                }

                $this->headers[$key] = array_merge((array) $existingHeader, [$value]);
            }
        };
    }
}

class DesignLibraryTest extends TestCase
{
    public function testHandleRequestReturnsSiteConfigPayload(): void
    {
        $request = $this->createMock(WP_REST_Request::class);

        $expectedPayload = [
            'website' => 'https://example.com',
            'dbVersion' => 52,
            'allowedSettingKeys' => ['tokens'],
            'allowedSettingKeyPrefixes' => ['header_'],
            'mods' => [
                'header_layout' => 'centered',
            ],
            'css' => '.site-header { color: #000; }',
        ];

        $endpoint = new class ($expectedPayload) extends DesignLibrary {
            public function __construct(private array $payload)
            {
            }

            protected function getSiteConfig(): array
            {
                return $this->payload;
            }
        };

        $response = $endpoint->handleRequest($request);

        $this->assertInstanceOf(WP_REST_Response::class, $response);
        $this->assertSame($expectedPayload, $response->data);
        $this->assertSame('*', $response->get_headers()['Access-Control-Allow-Origin'] ?? null);
        $this->assertSame('GET, OPTIONS', $response->get_headers()['Access-Control-Allow-Methods'] ?? null);
    }

    public function testPermissionCallbackReturnsTrue(): void
    {
        $endpoint = new class extends DesignLibrary {
            protected function getSiteConfig(): array
            {
                return [];
            }
        };

        $this->assertTrue($endpoint->permissionCallback());
    }
}
