<?php

namespace Municipio\MarkupProcessor\Processors;

use PHPUnit\Framework\TestCase;

class RemoveInvalidForAttributesProcessorTest extends TestCase
{
    public function testKeepsForwardReferencesAndRemovesMissingTargets(): void
    {
        $input = '<label for="later">Valid</label><label for="missing" class="x">Invalid</label>'
            . '<input id="later"><label for="">Empty</label>';

        $expected = '<label for="later">Valid</label><label class="x">Invalid</label>'
            . '<input id="later"><label>Empty</label>';

        $this->assertSame($expected, (new RemoveInvalidForAttributesProcessor())->process($input));
    }

    public function testMatchesDecodedIdsAndTreatsIdsAsCaseSensitive(): void
    {
        $input = '<label for="rock&amp;roll">Valid</label><label for="Rock&amp;roll">Invalid</label>'
            . '<input id="rock&amp;roll">';

        $expected = '<label for="rock&amp;roll">Valid</label><label>Invalid</label>'
            . '<input id="rock&amp;roll">';

        $this->assertSame($expected, (new RemoveInvalidForAttributesProcessor())->process($input));
    }

    public function testOutputRequiresEveryReferencedId(): void
    {
        $input = '<output for="first second">Valid</output><output for="first missing">Invalid</output>'
            . '<input id="first"><input id="second">';

        $expected = '<output for="first second">Valid</output><output>Invalid</output>'
            . '<input id="first"><input id="second">';

        $this->assertSame($expected, (new RemoveInvalidForAttributesProcessor())->process($input));
    }

    public function testIgnoresTextCommentsAndRawElements(): void
    {
        $input = '<!-- <input id="fake"> --><script>const text = "<input id=\\"fake\\">";</script>'
            . '<style>.x::after {content: "<input id=\'fake\'>"}</style>'
            . '<label for="fake">Invalid</label><label data-for="missing">Untouched</label>';

        $expected = str_replace('<label for="fake">', '<label>', $input);

        $this->assertSame($expected, (new RemoveInvalidForAttributesProcessor())->process($input));
    }

    public function testDoesNotMistakeOtherAttributesForIds(): void
    {
        $input = '<label for="x">Invalid</label><div data-id="x"></div>';

        $this->assertSame(
            '<label>Invalid</label><div data-id="x"></div>',
            (new RemoveInvalidForAttributesProcessor())->process($input),
        );
    }
}
