<?php

declare(strict_types=1);

namespace Municipio\Upgrade\V71;

use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

// Equivalent to WordPress's attribute encoder, without loading WordPress in unit tests.
function serialize_block_attributes(array $attributes): string
{
    return strtr(json_encode($attributes, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE), [
        '\\\\' => '\\u005c', '--' => '\\u002d\\u002d', '<' => '\\u003c',
        '>' => '\\u003e', '&' => '\\u0026', '\\"' => '\\u0022',
    ]);
}

class Version71Test extends TestCase
{
    public function testRestoresOnlyExactDamagedValuesAndPreservesOtherEdits(): void
    {
        $migration = new Version71(new FakeWpService());
        $original = '<p>One & two</p>' . "\r\n" . '<!-- nested -->';
        $encoded = stripslashes(serialize_block_attributes(['value' => $original]));
        $damaged = json_decode($encoded, true)['value'];
        $revision = [['blockName' => 'acf/section-full', 'attrs' => ['data' => ['text' => $original, 'other' => 'Old']], 'innerBlocks' => [], 'innerContent' => []]];
        $blocks = [['blockName' => 'acf/section-full', 'attrs' => ['data' => ['text' => $damaged, 'other' => 'New']], 'innerBlocks' => [], 'innerContent' => []]];
        static::assertTrue($migration->repairBlocks($blocks, [$revision]));
        static::assertSame($original, $blocks[0]['attrs']['data']['text']);
        static::assertSame('New', $blocks[0]['attrs']['data']['other']);
        static::assertFalse($migration->repairBlocks($blocks, [$revision]));
    }

    public function testDoesNotGuessWhenRevisionsDisagree(): void
    {
        $migration = new Version71(new FakeWpService());
        // Both a literal r followed by a newline and a carriage return
        // followed by n become "rn" when the escape backslash is lost.
        $blocks = [['blockName' => 'acf/section-full', 'attrs' => ['data' => ['text' => 'u0026rn']], 'innerBlocks' => []]];
        $revisions = [
            [['blockName' => 'acf/section-full', 'attrs' => ['data' => ['text' => "&r\n"]], 'innerBlocks' => []]],
            [['blockName' => 'acf/section-full', 'attrs' => ['data' => ['text' => "&\rn"]], 'innerBlocks' => []]],
        ];
        static::assertFalse($migration->repairBlocks($blocks, $revisions));
        static::assertSame('u0026rn', $blocks[0]['attrs']['data']['text']);
    }

    public function testRecoversNestedBlocksAndLeavesUnmatchedContentAlone(): void
    {
        $migration = new Version71(new FakeWpService());
        $blocks = [['blockName' => 'core/group', 'innerBlocks' => [
            ['blockName' => 'acf/section-full', 'attrs' => ['data' => ['text' => 'A u0026 B', 'title' => 'Edited']], 'innerBlocks' => [], 'innerContent' => []],
        ]]];
        $revision = [['blockName' => 'core/group', 'innerBlocks' => [
            ['blockName' => 'acf/section-full', 'attrs' => ['data' => ['text' => 'A & B', 'title' => 'Earlier']], 'innerBlocks' => [], 'innerContent' => []],
        ]]];
        static::assertTrue($migration->repairBlocks($blocks, [$revision]));
        static::assertSame('A & B', $blocks[0]['innerBlocks'][0]['attrs']['data']['text']);
        static::assertSame('Edited', $blocks[0]['innerBlocks'][0]['attrs']['data']['title']);
        $blocks[0]['innerBlocks'][0]['attrs']['data']['text'] = 'A u0026 B plus a later edit';
        static::assertFalse($migration->repairBlocks($blocks, [$revision]));
    }
}
