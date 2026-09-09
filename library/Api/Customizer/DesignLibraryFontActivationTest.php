<?php

declare(strict_types=1);

namespace Municipio\Api\Customizer;

use PHPUnit\Framework\TestCase;
use WP_Error;
use WP_REST_Request;
use WP_REST_Response;
use WpService\Implementations\FakeWpService;
use WpService\WpService;

if (!function_exists(__NAMESPACE__ . '\\is_user_logged_in')) {
    /**
     * Test double for is_user_logged_in.
     */
    function is_user_logged_in(): bool
    {
        return DesignLibraryFontActivationTestState::$isUserLoggedIn;
    }
}

if (!function_exists(__NAMESPACE__ . '\\current_user_can')) {
    /**
     * Test double for current_user_can.
     */
    function current_user_can(string $capability): bool
    {
        return DesignLibraryFontActivationTestState::$currentUserCan;
    }
}

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
        return new class($response) extends WP_REST_Response {
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

/**
 * Mutable state backing the is_user_logged_in()/current_user_can() test doubles above.
 */
class DesignLibraryFontActivationTestState
{
    public static bool $isUserLoggedIn = true;
    public static bool $currentUserCan = true;
}

class DesignLibraryFontActivationTest extends TestCase
{
    protected function setUp(): void
    {
        DesignLibraryFontActivationTestState::$isUserLoggedIn = true;
        DesignLibraryFontActivationTestState::$currentUserCan = true;
    }

    public function testPermissionCallbackRequiresLoggedInUserWithCustomizeCapability(): void
    {
        $endpoint = new DesignLibraryFontActivation();

        DesignLibraryFontActivationTestState::$isUserLoggedIn = false;
        $this->assertFalse($endpoint->permissionCallback());

        DesignLibraryFontActivationTestState::$isUserLoggedIn = true;
        DesignLibraryFontActivationTestState::$currentUserCan = false;
        $this->assertFalse($endpoint->permissionCallback());

        DesignLibraryFontActivationTestState::$currentUserCan = true;
        $this->assertTrue($endpoint->permissionCallback());
    }

    public function testHandleRequestDownloadsAndActivatesTheFontFace(): void
    {
        $wpService = new FakeWpService([
            'postTypeExists' => static fn(string $postType): bool => in_array($postType, ['wp_font_family', 'wp_font_face'], true),
            'sanitizeTitle' => static fn(string $title): string => strtolower(str_replace(' ', '-', $title)),
            'wpJsonEncode' => static fn(mixed $value): string|false => json_encode($value),
            'wpSlash' => static fn(string|array $value): string|array => is_string($value) ? addslashes($value) : $value,
            'isWpError' => false,
            'getPageByPath' => null,
            'downloadUrl' => static fn(string $url): string => '/tmp/roboto-700.woff2',
            'addFilter' => true,
            'removeFilter' => true,
            'wpHandleSideload' => static fn(array $file): array => [
                'file' => '/var/www/wp-content/uploads/fonts/roboto-700.woff2',
                'url' => 'https://example.com/wp-content/uploads/fonts/roboto-700.woff2',
                'type' => 'font/woff2',
            ],
            'wpGetFontDir' => [
                'basedir' => '/var/www/wp-content/uploads/fonts',
                'baseurl' => 'https://example.com/wp-content/uploads/fonts',
            ],
            'getPosts' => [],
            'wpInsertPost' => static fn(array $postarr): int => $postarr['post_type'] === 'wp_font_family' ? 41 : 42,
            'addPostMeta' => true,
        ]);

        $endpoint = new class($wpService) extends DesignLibraryFontActivation {
            public function __construct(
                private WpService $wpService,
            ) {}

            protected function getWpService(): WpService
            {
                return $this->wpService;
            }
        };

        $request = $this->createMock(WP_REST_Request::class);
        $request
            ->method('get_param')
            ->willReturnMap([
                ['fontFamily', 'Roboto'],
                ['fontWeight', '700'],
                ['fontStyle',  'normal'],
                ['url',        'https://fonts.example.com/roboto-700.woff2'],
            ]);

        $response = $endpoint->handleRequest($request);

        $this->assertInstanceOf(WP_REST_Response::class, $response);
        $this->assertSame(['success' => true, 'fontFamilyId' => 41], $response->data);
        $this->assertSame(
            ['wp_font_family', 'wp_font_face'],
            array_values(array_map(
                static fn(array $call): string => $call[0]['post_type'],
                $wpService->methodCalls['wpInsertPost'],
            )),
        );
        $this->assertSame(
            [[42, '_wp_font_face_file', 'roboto-700.woff2']],
            $wpService->methodCalls['addPostMeta'],
        );
    }

    public function testHandleRequestReturnsErrorWhenMissingParams(): void
    {
        $endpoint = new DesignLibraryFontActivation();

        $request = $this->createMock(WP_REST_Request::class);
        $request
            ->method('get_param')
            ->willReturnMap([
                ['fontFamily', ''],
                ['fontWeight', '400'],
                ['fontStyle',  'normal'],
                ['url',        ''],
            ]);

        $response = $endpoint->handleRequest($request);

        $this->assertInstanceOf(WP_Error::class, $response);
    }
}
