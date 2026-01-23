<?php

namespace Municipio\Helper;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class GoogleTranslateTest extends TestCase
{
    #[TestDox('wraps targeted words')]
    public function testWrapsTargetedWords()
    {
        $content = 'This is a test string.';
        $words = ['test'];

        $expected = 'This is a <span translate="no">test</span> string.';
        $result = GoogleTranslate::wrapWordsInContent($content, $words);
        static::assertSame($expected, $result);
    }

    #[TestDox('does not wrap when word is already wrapped')]
    public function testDoesNotWrapWhenWordIsAlreadyWrapped()
    {
        $content = 'This is a <span translate="no">test</span> string.';
        $words = ['test'];

        $result = GoogleTranslate::wrapWordsInContent($content, $words);
        static::assertSame($content, $result);
    }
}
