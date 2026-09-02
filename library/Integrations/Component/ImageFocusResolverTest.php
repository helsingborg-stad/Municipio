<?php

namespace Municipio\Integrations\Component;

use PHPUnit\Framework\TestCase;

function apply_filters($hookName, $focusPoint, $imageId)
{
    $GLOBALS['image_focus_resolver_filter_calls']++;

    return [
        'left' => $imageId,
        'top' => $imageId,
    ];
}

function get_current_blog_id()
{
    return $GLOBALS['image_focus_resolver_blog_id'];
}

class ImageFocusResolverTest extends TestCase
{
    protected function setUp(): void
    {
        $GLOBALS['image_focus_resolver_filter_calls'] = 0;
        $GLOBALS['image_focus_resolver_blog_id'] = 1;
    }

    public function testCachesFocusPointForEquivalentRatios(): void
    {
        $first = new ImageFocusResolver(['id' => 101, 'ratio' => [800, 600]]);
        $second = new ImageFocusResolver(['id' => 101, 'ratio' => [400, 300]]);

        $this->assertSame($first->getFocusPoint(), $second->getFocusPoint());
        $this->assertSame(1, $GLOBALS['image_focus_resolver_filter_calls']);
    }

    public function testDoesNotShareFocusPointBetweenDifferentRatios(): void
    {
        $first = new ImageFocusResolver(['id' => 102, 'ratio' => [800, 600]]);
        $second = new ImageFocusResolver(['id' => 102, 'ratio' => [800, 450]]);

        $first->getFocusPoint();
        $second->getFocusPoint();

        $this->assertSame(2, $GLOBALS['image_focus_resolver_filter_calls']);
    }

    public function testDoesNotShareFocusPointBetweenBlogs(): void
    {
        $first = new ImageFocusResolver(['id' => 103, 'ratio' => [800, 600]]);
        $first->getFocusPoint();

        $GLOBALS['image_focus_resolver_blog_id'] = 2;
        $second = new ImageFocusResolver(['id' => 103, 'ratio' => [400, 300]]);
        $second->getFocusPoint();

        $this->assertSame(2, $GLOBALS['image_focus_resolver_filter_calls']);
    }
}
