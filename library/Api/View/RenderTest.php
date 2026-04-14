<?php

declare(strict_types=1);

namespace Municipio\Api\View;

use PHPUnit\Framework\TestCase;
use WP_REST_Request;
use WP_REST_Response;

if (!function_exists(__NAMESPACE__ . '\render_test_state')) {
    /**
     * Gets or updates shared test state for the Render endpoint tests.
     *
     * @param array<string, mixed>|null $state The state update.
     *
     * @return array<string, mixed>
     */
    function render_test_state(?array $state = null): array
    {
        static $renderState = [
            'calls'     => [],
            'markup'    => '<div>Rendered markup</div>',
            'throwable' => null,
        ];

        if ($state !== null) {
            $renderState = array_merge($renderState, $state);
        }

        return $renderState;
    }
}

if (!function_exists(__NAMESPACE__ . '\render_blade_view')) {
    /**
     * Test double for the blade rendering helper.
     *
     * @param string $view The view to render.
     * @param array<string, mixed> $data The view data.
     * @param bool $overrideViewPaths Whether to override view paths.
     * @param bool $formatError Whether to format render errors.
     */
    function render_blade_view($view, $data = [], $overrideViewPaths = false, $formatError = true): string
    {
        $state = render_test_state();
        render_test_state([
            'calls' => [
                ...$state['calls'],
                [
                    'view'              => $view,
                    'data'              => $data,
                    'overrideViewPaths' => $overrideViewPaths,
                    'formatError'       => $formatError,
                ],
            ],
        ]);

        if ($state['throwable'] instanceof \Throwable) {
            throw $state['throwable'];
        }

        return $state['markup'];
    }
}

if (!function_exists(__NAMESPACE__ . '\rest_ensure_response')) {
    /**
     * Test double for the REST response helper.
     *
     * @param mixed $response The response payload.
     *
     * @return WP_REST_Response|\WP_Error
     */
    function rest_ensure_response($response)
    {
        if ($response instanceof \WP_Error) {
            return $response;
        }

        return new class ($response) extends WP_REST_Response {
            /**
             * @param mixed $data The response payload.
             */
            public function __construct($data = null)
            {
                $this->data    = $data;
                $this->status  = 200;
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

                $this->headers[$key] = [(array) $existingHeader, $value];
            }
        };
    }
}

/**
 * @internal
 */
class RenderTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        render_test_state([
            'calls'     => [],
            'markup'    => '<div>Rendered markup</div>',
            'throwable' => null,
        ]);
    }

    public function testHandleRequestSendsNoCacheHeadersAndReturnsRenderedMarkup(): void
    {
        $request = $this->createMock(WP_REST_Request::class);
        $request->method('get_params')->with('GET')->willReturn([
            'view' => 'partials.example',
            'data' => ['name' => 'Municipio'],
        ]);

        $endpoint = new class extends Render {
            public bool $noCacheHeadersSent = false;

            protected function sendNoCacheHeaders(): void
            {
                $this->noCacheHeadersSent = true;
            }
        };

        $response = $endpoint->handleRequest($request);

        static::assertTrue($endpoint->noCacheHeadersSent);
        static::assertInstanceOf(WP_REST_Response::class, $response);
        static::assertSame('<div>Rendered markup</div>', $response->data);
        static::assertSame([
            [
                'view'              => 'partials.example',
                'data'              => ['name' => 'Municipio'],
                'overrideViewPaths' => false,
                'formatError'       => false,
            ],
        ], render_test_state()['calls']);
    }

    public function testHandleRequestSendsNoCacheHeadersAndReturnsErrorOnRenderFailure(): void
    {
        $request = $this->createMock(WP_REST_Request::class);
        $request->method('get_params')->with('GET')->willReturn([
            'view' => 'partials.example',
            'data' => [],
        ]);

        $endpoint = new class extends Render {
            public bool $noCacheHeadersSent = false;

            protected function sendNoCacheHeaders(): void
            {
                $this->noCacheHeadersSent = true;
            }
        };

        render_test_state([
            'throwable' => new \RuntimeException('Rendering failed'),
        ]);

        $response = $endpoint->handleRequest($request);

        static::assertTrue($endpoint->noCacheHeadersSent);
        static::assertInstanceOf(\WP_Error::class, $response);
    }
}
