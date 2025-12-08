<?php

declare(strict_types=1);

namespace Municipio\PostsList\ViewCallableProviders;

use Municipio\PostObject\NullPostObject;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Contracts\WpTrimWords;

class GetExcerptTest extends TestCase
{
    #[TestDox('returns a callable that applies wpTrimWords to the post excerpt')]
    public function testGetCallable(): void
    {
        $wpService = new class implements WpTrimWords {
            public array $calls = [];

            public function wpTrimWords(string $text, int $numWords = 55, null|string $more = null): string
            {
                $this->calls[] = ['text' => $text, 'numWords' => $numWords, 'more' => $more];
                return 'trimmed excerpt';
            }
        };

        $post = new class extends NullPostObject {
            public function getExcerpt(): string
            {
                return 'test excerpt';
            }
        };

        $getExcerpt = new GetExcerpt($wpService);
        $callable = $getExcerpt->getCallable();
        $result = $callable($post, 10);

        static::assertSame('trimmed excerpt', $result);
        static::assertCount(1, $wpService->calls);
        static::assertSame('test excerpt', $wpService->calls[0]['text']);
        static::assertSame(10, $wpService->calls[0]['numWords']);
        static::assertNull($wpService->calls[0]['more']);
    }
}
