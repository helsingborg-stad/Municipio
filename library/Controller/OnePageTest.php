<?php

declare(strict_types=1);

namespace Municipio\Controller;

use PHPUnit\Framework\TestCase;

final class OnePageTest extends TestCase
{
    public function testShouldRenderPostContentReturnsTrueWhenPostExists(): void
    {
        $controller = (new \ReflectionClass(OnePage::class))->newInstanceWithoutConstructor();
        $controller->data['post'] = (object) ['postContentFiltered' => '<p>Classic editor content</p>'];

        static::assertTrue($controller->shouldRenderPostContent());
    }

    public function testShouldRenderPostContentReturnsFalseWhenPostIsMissing(): void
    {
        $controller = (new \ReflectionClass(OnePage::class))->newInstanceWithoutConstructor();

        static::assertFalse($controller->shouldRenderPostContent());
    }
}
