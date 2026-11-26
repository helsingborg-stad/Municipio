<?php

declare(strict_types=1);

namespace Modularity\Helper\FragmentCache;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class FragmentTest extends TestCase
{
    #[TestDox('fromArray() restores a fragment from toArray()')]
    public function testFromArrayRestoresAFragmentFromToArray(): void
    {
        $fragment = new Fragment('<p>A</p>', ['components' => ['collection']]);

        static::assertEquals($fragment, Fragment::fromArray($fragment->toArray()));
    }

    #[TestDox('fromArray() returns null for $_dataName')]
    #[DataProvider('malformedDataProvider')]
    public function testFromArrayReturnsNullForMalformedData(mixed $data): void
    {
        static::assertNull(Fragment::fromArray($data));
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function malformedDataProvider(): array
    {
        return [
            'a value that is not an array' => ['<p>A</p>'],
            'the legacy one-array-per-post format' => [['5qkvavqnmru0' => '<p>A</p>']],
            'missing assets' => [['html' => '<p>A</p>']],
            'html that is not a string' => [['html' => null, 'assets' => []]],
        ];
    }
}
