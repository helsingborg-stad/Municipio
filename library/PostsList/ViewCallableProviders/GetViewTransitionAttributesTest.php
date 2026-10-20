<?php

declare(strict_types=1);

namespace Municipio\PostsList\ViewCallableProviders;

use PHPUnit\Framework\TestCase;

class GetViewTransitionAttributesTest extends TestCase
{
    public function testItCreatesStableNamesForATargetPost(): void
    {
        $attributes = (new GetViewTransitionAttributes())->getCallable()(42);

        static::assertSame('post', $attributes['data-view-transition-target']);
        static::assertSame(
            '--municipio-view-transition-title: municipio-post-42-title; --municipio-view-transition-image: municipio-post-42-image;',
            $attributes['style'],
        );
    }
}
