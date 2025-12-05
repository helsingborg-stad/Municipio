<?php

declare(strict_types=1);

namespace Municipio\PostObject\ExcerptResolver;

use Municipio\PostObject\NullPostObject;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Contracts\StripShortcodes;
use WpService\Contracts\WpStripAllTags;

class ExcerptResolverTest extends TestCase
{
    #[TestDox('resolves excerpt from getExcerpt() if available')]
    public function testResolveExcerptFromGetExcerpt(): void
    {
        $post = new class extends NullPostObject {
            public function getExcerpt(): string
            {
                return 'excerpt from getExcerpt()';
            }
        };

        $excerptResolver = new ExcerptResolver($this->getWpService());
        static::assertSame('excerpt from getExcerpt()', $excerptResolver->resolveExcerpt($post));
    }

    #[TestDox('resolves excerpt from getContent() if getExcerpt() is empty')]
    public function testResolveExcerptFromGetContent(): void
    {
        $post = new class extends NullPostObject {
            public function getExcerpt(): string
            {
                return '';
            }

            public function getContent(): string
            {
                return 'content from getContent()';
            }
        };

        $excerptResolver = new ExcerptResolver($this->getWpService());
        static::assertSame('content from getContent()', $excerptResolver->resolveExcerpt($post));
    }

    #[TestDox('returns empty string if both getExcerpt() and getContent() are empty')]
    public function testReturnsEmptyStringIfBothGetExcerptAndGetContentAreEmpty(): void
    {
        $post = new class extends NullPostObject {
            public function getExcerpt(): string
            {
                return '';
            }

            public function getContent(): string
            {
                return '';
            }
        };

        $excerptResolver = new ExcerptResolver($this->getWpService());
        static::assertSame('', $excerptResolver->resolveExcerpt($post));
    }

    #[TestDox('applies nl2br and trims the excerpt')]
    public function testAppliesNl2brAndTrimsTheExcerpt(): void
    {
        $post = new class extends NullPostObject {
            public function getExcerpt(): string
            {
                return " line 1\nline 2 ";
            }
        };

        $excerptResolver = new ExcerptResolver($this->getWpService());
        static::assertSame("line 1<br />\nline 2", $excerptResolver->resolveExcerpt($post));
    }

    #[TestDox('applies sanitization to the excerpt using the wpService')]
    public function testAppliesSanitizationToTheExcerptUsingTheWpService(): void
    {
        $post = new class extends NullPostObject {
            public function getExcerpt(): string
            {
                return '<p>test[shortcode]excerpt</p>';
            }
        };

        $excerptResolver = new ExcerptResolver($this->getWpService());
        static::assertSame('testexcerpt', $excerptResolver->resolveExcerpt($post));
    }

    #[TestDox('returns content before <!--more--> tag if present')]
    public function testHandlesMoreTagCorrectly(): void
    {
        $post = new class extends NullPostObject {
            public function getExcerpt(): string
            {
                return 'Before more tag.<!--more--> After more tag.';
            }
        };

        $excerptResolver = new ExcerptResolver($this->getWpService());
        static::assertSame('Before more tag.', $excerptResolver->resolveExcerpt($post));
    }

    private function getWpService(): StripShortcodes&WpStripAllTags
    {
        return new class implements StripShortcodes, WpStripAllTags {
            public function stripShortcodes(string $text): string
            {
                return str_replace('[shortcode]', '', $text);
            }

            // @mago-expect lint:no-boolean-flag-parameter
            public function wpStripAllTags(string $text, bool $removeBreaks = true): string
            {
                $text = strip_tags($text);
                if ($removeBreaks) {
                    $text = str_replace(["\r", "\n"], ' ', $text);
                }
                return $text;
            }
        };
    }
}
