<?php

namespace Municipio\PostsList\ViewCallableProviders;

use Municipio\PostObject\NullPostObject;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class GetExcerptWithoutLinksTest extends TestCase
{
    #[TestDox('removes <a> tags from excerpt')]
    public function testRemovesATagsFromExcerpt(): void
    {
        $post = new class extends NullPostObject {
            public function getExcerpt(): string
            {
                return 'This is a <a href="#">link</a> in the excerpt.';
            }
        };

        $viewUtility = new GetExcerptWithoutLinks();

        $this->assertSame('This is a link in the excerpt.', $viewUtility->getCallable()($post));
    }
}
