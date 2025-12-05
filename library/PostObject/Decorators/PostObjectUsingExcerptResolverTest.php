<?php

declare(strict_types=1);

namespace Municipio\PostObject\Decorators;

use Municipio\PostObject\ExcerptResolver\ExcerptResolverInterface;
use Municipio\PostObject\NullPostObject;
use Municipio\PostObject\PostObjectInterface;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class PostObjectUsingExcerptResolverTest extends TestCase
{
    #[TestDox('It should return excerpt from ExcerptResolver')]
    public function testGetExcerpt(): void
    {
        $post = new class extends NullPostObject {};
        $excerptResolver = new class implements ExcerptResolverInterface {
            public function resolveExcerpt(PostObjectInterface $postObject): string
            {
                return 'test excerpt';
            }
        };

        $postObjectUsingExcerptResolver = new PostObjectUsingExcerptResolver($post, $excerptResolver);
        static::assertSame('test excerpt', $postObjectUsingExcerptResolver->getExcerpt());
    }
}
