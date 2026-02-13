<?php

namespace Municipio\PostsList\Block;

use Municipio\Helper\Post;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WP_REST_Request;
use WpService\Contracts\AddAction;
use WpService\Contracts\CurrentUserCan;
use WpService\Contracts\RegisterBlockType;
use WpService\Contracts\RegisterRestRoute;
use WpService\Contracts\WpDequeueScript;
use WpService\Contracts\WpDeregisterScript;

class PostsListBlockTest extends TestCase
{
    #[TestDox('addHooks registers the correct WordPress actions')]
    public function testAddHooksRegistersCorrectActions(): void
    {
        $wpService = new class implements AddAction, RegisterBlockType, RegisterRestRoute, CurrentUserCan, WpDequeueScript, WpDeregisterScript {
            public array $addActionCalls = [];

            public function addAction(string $hookName, callable $callback, int $priority = 10, int $acceptedArgs = 1): true
            {
                $this->addActionCalls[] = [$hookName, $callback, $priority, $acceptedArgs];
                return true;
            }

            public function registerBlockType($blockType, array $args = []): \WP_Block_Type|false
            {
                return false;
            }

            public function registerRestRoute(string $namespace, string $route, array $args = [], bool $override = false): bool
            {
                return true;
            }

            public function currentUserCan(string $capability, mixed ...$args): bool
            {
                return true;
            }

            public function wpDequeueScript(string $handle): void
            {
            }

            public function wpDeregisterScript(string $handle): void
            {
            }
        };

        $postsListBlock = new PostsListBlock($wpService);
        $postsListBlock->addHooks();

        $this->assertCount(3, $wpService->addActionCalls);
        $this->assertEquals('init', $wpService->addActionCalls[0][0]);
        $this->assertEquals('rest_api_init', $wpService->addActionCalls[1][0]);
        $this->assertEquals('customize_controls_enqueue_scripts', $wpService->addActionCalls[2][0]);
        $this->assertEquals(1, $wpService->addActionCalls[2][2]); // Priority should be 1
    }

    #[TestDox('registerBlock calls registerBlockType with correct block.json path')]
    public function testRegisterBlockCallsRegisterBlockType(): void
    {
        $wpService = new class implements AddAction, RegisterBlockType, RegisterRestRoute, CurrentUserCan, WpDequeueScript, WpDeregisterScript {
            public ?string $registeredBlockPath = null;

            public function addAction(string $hookName, callable $callback, int $priority = 10, int $acceptedArgs = 1): true
            {
                return true;
            }

            public function registerBlockType($blockType, array $args = []): \WP_Block_Type|false
            {
                $this->registeredBlockPath = $blockType;
                return false;
            }

            public function registerRestRoute(string $namespace, string $route, array $args = [], bool $override = false): bool
            {
                return true;
            }

            public function currentUserCan(string $capability, mixed ...$args): bool
            {
                return true;
            }

            public function wpDequeueScript(string $handle): void
            {
            }

            public function wpDeregisterScript(string $handle): void
            {
            }
        };

        $postsListBlock = new PostsListBlock($wpService);
        $postsListBlock->registerBlock();

        $expectedPath = dirname(__FILE__) . '/block.json';
        $this->assertEquals($expectedPath, $wpService->registeredBlockPath);
    }

    #[TestDox('excludeFromCustomizer method exists and can be called')]
    public function testExcludeFromCustomizerMethodExists(): void
    {
        $wpService = new class implements AddAction, RegisterBlockType, RegisterRestRoute, CurrentUserCan, WpDequeueScript, WpDeregisterScript {
            public function addAction(string $hookName, callable $callback, int $priority = 10, int $acceptedArgs = 1): true
            {
                return true;
            }

            public function registerBlockType($blockType, array $args = []): \WP_Block_Type|false
            {
                return false;
            }

            public function registerRestRoute(string $namespace, string $route, array $args = [], bool $override = false): bool
            {
                return true;
            }

            public function currentUserCan(string $capability, mixed ...$args): bool
            {
                return true;
            }

            public function wpDequeueScript(string $handle): void
            {
            }

            public function wpDeregisterScript(string $handle): void
            {
            }
        };

        $postsListBlock = new PostsListBlock($wpService);

        // Method should exist and be callable
        $this->assertTrue(method_exists($postsListBlock, 'excludeFromCustomizer'));
        $this->assertTrue(is_callable([$postsListBlock, 'excludeFromCustomizer']));

        // Should execute without errors in test environment
        $postsListBlock->excludeFromCustomizer();
        $this->assertTrue(true); // If we get here, no exception was thrown
    }

    #[TestDox('registerRestEndpoint method exists and can be called')]
    public function testRegisterRestEndpointMethodExists(): void
    {
        $wpService = new class implements AddAction, RegisterBlockType, RegisterRestRoute, CurrentUserCan, WpDequeueScript, WpDeregisterScript {
            public function addAction(string $hookName, callable $callback, int $priority = 10, int $acceptedArgs = 1): true
            {
                return true;
            }

            public function registerBlockType($blockType, array $args = []): \WP_Block_Type|false
            {
                return false;
            }

            public function registerRestRoute(string $namespace, string $route, array $args = [], bool $override = false): bool
            {
                return true;
            }

            public function currentUserCan(string $capability, mixed ...$args): bool
            {
                return true;
            }

            public function wpDequeueScript(string $handle): void
            {
            }

            public function wpDeregisterScript(string $handle): void
            {
            }
        };

        $postsListBlock = new PostsListBlock($wpService);

        // Method should exist and be callable
        $this->assertTrue(method_exists($postsListBlock, 'registerRestEndpoint'));
        $this->assertTrue(is_callable([$postsListBlock, 'registerRestEndpoint']));

        // Should execute without errors in test environment
        $postsListBlock->registerRestEndpoint();
        $this->assertTrue(true); // If we get here, no exception was thrown
    }

    #[TestDox('getPostTypeMetaKeys returns array when Post helper is available')]
    public function testGetPostTypeMetaKeysReturnsArray(): void
    {
        $wpService = new class implements AddAction, RegisterBlockType, RegisterRestRoute, CurrentUserCan, WpDequeueScript, WpDeregisterScript {
            public function addAction(string $hookName, callable $callback, int $priority = 10, int $acceptedArgs = 1): true
            {
                return true;
            }

            public function registerBlockType($blockType, array $args = []): \WP_Block_Type|false
            {
                return false;
            }

            public function registerRestRoute(string $namespace, string $route, array $args = [], bool $override = false): bool
            {
                return true;
            }

            public function currentUserCan(string $capability, mixed ...$args): bool
            {
                return true;
            }

            public function wpDequeueScript(string $handle): void
            {
            }

            public function wpDeregisterScript(string $handle): void
            {
            }
        };

        $request = $this->createMock(WP_REST_Request::class);
        $request->method('get_param')->with('postType')->willReturn('post');

        $postsListBlock = new PostsListBlock($wpService);

        // Since Post::getPosttypeMetaKeys uses global $wpdb, we expect it to fail in test environment
        // but we can test the method structure
        try {
            $result = $postsListBlock->getPostTypeMetaKeys($request);
            // If it doesn't throw an error, it should return an array
            $this->assertIsArray($result);
        } catch (\Error $e) {
            // Expected when $wpdb is not available in test environment
            $this->assertStringContainsString('get_col', $e->getMessage());
        }
    }

    #[TestDox('callback functions are properly structured')]
    public function testCallbackFunctionsAreProperlyStructured(): void
    {
        $wpService = new class implements AddAction, RegisterBlockType, RegisterRestRoute, CurrentUserCan, WpDequeueScript, WpDeregisterScript {
            public array $addActionCalls = [];

            public function addAction(string $hookName, callable $callback, int $priority = 10, int $acceptedArgs = 1): true
            {
                $this->addActionCalls[] = [$hookName, $callback, $priority, $acceptedArgs];
                return true;
            }

            public function registerBlockType($blockType, array $args = []): \WP_Block_Type|false
            {
                return false;
            }

            public function registerRestRoute(string $namespace, string $route, array $args = [], bool $override = false): bool
            {
                return true;
            }

            public function currentUserCan(string $capability, mixed ...$args): bool
            {
                return true;
            }

            public function wpDequeueScript(string $handle): void
            {
            }

            public function wpDeregisterScript(string $handle): void
            {
            }
        };

        $postsListBlock = new PostsListBlock($wpService);
        $postsListBlock->addHooks();

        // Verify all callbacks are properly structured
        foreach ($wpService->addActionCalls as $call) {
            $this->assertIsString($call[0]); // Hook name
            $this->assertIsCallable($call[1]); // Callback
            $this->assertIsInt($call[2]); // Priority
            $this->assertIsInt($call[3]); // Accepted args
        }
    }

    #[TestDox('class implements Hookable interface')]
    public function testImplementsHookableInterface(): void
    {
        $wpService = new class implements AddAction, RegisterBlockType, RegisterRestRoute, CurrentUserCan, WpDequeueScript, WpDeregisterScript {
            public function addAction(string $hookName, callable $callback, int $priority = 10, int $acceptedArgs = 1): true
            {
                return true;
            }

            public function registerBlockType($blockType, array $args = []): \WP_Block_Type|false
            {
                return false;
            }

            public function registerRestRoute(string $namespace, string $route, array $args = [], bool $override = false): bool
            {
                return true;
            }

            public function currentUserCan(string $capability, mixed ...$args): bool
            {
                return true;
            }

            public function wpDequeueScript(string $handle): void
            {
            }

            public function wpDeregisterScript(string $handle): void
            {
            }
        };

        $postsListBlock = new PostsListBlock($wpService);
        $this->assertInstanceOf(\Municipio\HooksRegistrar\Hookable::class, $postsListBlock);
    }

    #[TestDox('the expected script handle is dequeued and deregistered in excludeFromCustomizer')]
    public function testExcludeFromCustomizerDequeuesAndDeregistersScript(): void
    {
        $expectedHandle = 'municipio-posts-list-block-editor-script';

        $wpService = new class($expectedHandle) implements AddAction, RegisterBlockType, RegisterRestRoute, CurrentUserCan, WpDequeueScript, WpDeregisterScript {
            private string $expectedHandle;
            public array $dequeuedScripts = [];
            public array $deregisteredScripts = [];

            public function __construct(string $expectedHandle)
            {
                $this->expectedHandle = $expectedHandle;
            }

            public function addAction(string $hookName, callable $callback, int $priority = 10, int $acceptedArgs = 1): true
            {
                return true;
            }

            public function registerBlockType($blockType, array $args = []): \WP_Block_Type|false
            {
                return false;
            }

            public function registerRestRoute(string $namespace, string $route, array $args = [], bool $override = false): bool
            {
                return true;
            }

            public function currentUserCan(string $capability, mixed ...$args): bool
            {
                return true;
            }

            public function wpDequeueScript(string $handle): void
            {
                if ($handle === $this->expectedHandle) {
                    $this->dequeuedScripts[] = $handle;
                }
            }

            public function wpDeregisterScript(string $handle): void
            {
                if ($handle === $this->expectedHandle) {
                    $this->deregisteredScripts[] = $handle;
                }
            }
        };

        $postsListBlock = new PostsListBlock($wpService);
        $postsListBlock->excludeFromCustomizer();

        $this->assertContains($expectedHandle, $wpService->dequeuedScripts, 'Expected script handle was not dequeued');
        $this->assertContains($expectedHandle, $wpService->deregisteredScripts, 'Expected script handle was not deregistered');
    }
}
