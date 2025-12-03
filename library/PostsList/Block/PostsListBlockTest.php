<?php

namespace Municipio\PostsList\Block;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WP_Block;
use WP_Block_Type;
use WpService\Contracts\AddAction;
use WpService\Contracts\RegisterBlockType;

class PostsListBlockTest extends TestCase
{
    #[TestDox('calls registerBlock on init')]
    public function testAddHooks(): void
    {
        $wpService = new class implements AddAction, RegisterBlockType {
            public array $addActionCalls = [];

            public function addAction(
                string $hookName,
                callable $callback,
                int $priority = 10,
                int $acceptedArgs = 1,
            ): true {
                $this->addActionCalls[] = [
                    'hookName' => $hookName,
                    'callback' => $callback,
                ];
                return true;
            }

            public function registerBlockType(string|WP_Block_Type $blockType, array $args = []): WP_Block_Type|false
            {
                return false;
            }
        };

        $postsListBlock = new PostsListBlock($wpService, $this->createBlockRenderer());
        $postsListBlock->addHooks();

        $this->assertEquals('init', $wpService->addActionCalls[0]['hookName']);
    }

    #[TestDox('registers block with valid block json file and render callback')]
    public function testRegisterBlock(): void
    {
        $wpService = new class implements AddAction, RegisterBlockType {
            public null|string $registeredBlockType = null;
            public null|array $registeredArgs = null;

            public function addAction(
                string $hookName,
                callable $callback,
                int $priority = 10,
                int $acceptedArgs = 1,
            ): true {
                return true;
            }

            public function registerBlockType(string|WP_Block_Type $blockType, array $args = []): WP_Block_Type|false
            {
                $this->registeredBlockType = is_string($blockType) ? $blockType : null;
                $this->registeredArgs = $args;
                return false;
            }
        };

        $postsListBlock = new PostsListBlock($wpService, $this->createBlockRenderer());
        $postsListBlock->registerBlock();

        $this->assertFileExists($wpService->registeredBlockType);
        $this->assertIsCallable($wpService->registeredArgs['render_callback']);
    }

    private function createBlockRenderer(): BlockRendererInterface
    {
        return new class implements BlockRendererInterface {
            public function render(array $attributes, string $content, WP_Block $block): string
            {
                return '';
            }
        };
    }
}
