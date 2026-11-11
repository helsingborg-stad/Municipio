<?php

declare(strict_types=1);

namespace Municipio\Upgrade\V70;

use Municipio\Helper\ColorSwatches;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class Version70Test extends TestCase
{
    public function testMigratesSectionPostMetadata(): void
    {
        $service = new FakeWpService([
            'getPosts' => static fn (array $args): array => is_array($args['post_type']) ? [12] : [],
            'getPostMeta' => '#ffffff',
            'updatePostMeta' => true,
        ]);
        (new Version70($service))->upgradeToVersion();
        static::assertSame([[12, 'background_color', ColorSwatches::getClosestColorPairRole('#ffffff')]], $service->methodCalls['updatePostMeta']);
    }

    public function testMigratesNestedSectionsAndContainerGradientsIdempotently(): void
    {
        $service = new FakeWpService();
        $migration = new Version70($service);
        $blocks = [[
            'blockName' => 'core/group',
            'innerBlocks' => [
                ['blockName' => 'acf/section-split', 'attrs' => ['data' => ['background_color' => '#ffffff', 'text_color' => 'dark']], 'innerBlocks' => []],
                ['blockName' => 'acf/container', 'attrs' => ['data' => ['color' => '#ffffff', 'background_gradient_0_color' => '#ffffff', 'background_gradient' => [['color' => '#ffffff']]]], 'innerBlocks' => []],
                ['blockName' => 'acf/other', 'attrs' => ['data' => ['background_color' => '#ffffff']], 'innerBlocks' => []],
            ],
        ]];
        $expected = ColorSwatches::getClosestColorPairRole('#ffffff');
        static::assertTrue($migration->migrateBlocks($blocks));
        static::assertSame($expected, $blocks[0]['innerBlocks'][0]['attrs']['data']['background_color']);
        static::assertSame('dark', $blocks[0]['innerBlocks'][0]['attrs']['data']['text_color']);
        static::assertSame($expected, $blocks[0]['innerBlocks'][1]['attrs']['data']['background_gradient_0_color']);
        static::assertSame($expected, $blocks[0]['innerBlocks'][1]['attrs']['data']['background_gradient'][0]['color']);
        static::assertSame('#ffffff', $blocks[0]['innerBlocks'][2]['attrs']['data']['background_color']);
        static::assertArrayNotHasKey('attrs', $blocks[0]);
        static::assertFalse($migration->migrateBlocks($blocks));
    }
}
