<?php

declare(strict_types=1);

namespace Municipio\SearchIndex\Index\Record\PropertyMappers;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

/**
 * Normalizes text for use in search records.
 */
class SanitizesTextTest extends TestCase
{
    #[TestDox('Sanitizes text by removing markup and normalizing whitespace')]
    public function testSanitizesText(): void
    {
        $sanitizer = new class {
            use SanitizesText;
            public function publicSanitizeText(string $text): string
            {
                return $this->sanitizeText($text);
            }
        };

        $input = "<style some=\"foo\">ignored</style><script>ignored</script><p>   Content   </p>";

        static::assertSame("Content", $sanitizer->publicSanitizeText($input));
    }
}