<?php

declare(strict_types=1);

namespace Modularity\Helper\FragmentCache;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class BlockFragmentInputTest extends TestCase
{
    #[TestDox('fromBlock() keeps the plain values of the block')]
    public function testFromBlockKeepsPlainValues(): void
    {
        $block = [
            'id' => 'block_abc',
            'name' => 'acf/text',
            'anchor' => 'intro',
            'className' => 'is-style-card',
            'data' => ['text' => 'Hello', 'format' => 'date', 'count' => 3, 'show' => true],
        ];

        static::assertSame($block, BlockFragmentInput::fromBlock($block));
    }

    #[TestDox('fromBlock() drops objects, such as the render callback instance')]
    public function testFromBlockDropsObjects(): void
    {
        $renderer = new \stdClass();

        static::assertSame(
            ['name' => 'acf/text', 'render_callback' => [1 => 'renderBlock'], 'data' => ['text' => 'Hello']],
            BlockFragmentInput::fromBlock([
                'name' => 'acf/text',
                'render_callback' => [$renderer, 'renderBlock'],
                'enqueue_assets' => static fn() => null,
                'data' => ['text' => 'Hello', 'post' => $renderer],
            ]),
        );
    }

    #[TestDox('fromBlock() gives the same input when the state of the render callback instance changes')]
    public function testFromBlockIgnoresRenderCallbackState(): void
    {
        $renderer = new \stdClass();
        $block = ['name' => 'acf/text', 'render_callback' => [$renderer, 'renderBlock']];
        $before = BlockFragmentInput::fromBlock($block);

        $renderer->classes = ['posts' => ['data' => 'Post queried in this request']];

        static::assertSame($before, BlockFragmentInput::fromBlock($block));
    }
}
