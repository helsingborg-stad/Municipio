<?php

declare(strict_types=1);

namespace Municipio\Upgrade\Palette;

use AcfService\Contracts\GetField;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class PopulateUsedColorsTest extends TestCase
{
    public function testCountsSupportedBlockFormsWithoutCountingUnrelatedColors(): void
    {
        $seed = new PopulateUsedColors(new FakeWpService(), $this->createMock(GetField::class));
        $counts = [];
        $seed->countBlocks($counts, [[
            'blockName' => 'core/group', 'innerBlocks' => [
                ['blockName' => 'acf/section-full', 'attrs' => ['data' => ['field_61543393334bf' => '#abc']]],
                ['blockName' => 'acf/container', 'attrs' => ['data' => ['color' => '#aabbcc', 'background_gradient_0_color' => 'rgb(20, 30, 40)', 'background_gradient_1_color' => 'rgba(1, 2, 3, 0.5)']]],
                ['blockName' => 'acf/manualinput', 'attrs' => ['data' => ['field_64ff22b2d91b7' => [['field_689b2cf733d44' => '#123456']]]]],
                ['blockName' => 'acf/other', 'attrs' => ['data' => ['color' => '#000000']]],
            ],
        ]]);
        static::assertSame(['#aabbcc' => 2, '#141e28' => 1, '#123456' => 1], $counts);
    }

    public function testPopulatesFreeSlotsByFrequencyWithoutOverwritingConfiguredPairs(): void
    {
        $seed = new PopulateUsedColors(new FakeWpService(), $this->createMock(GetField::class));
        $tokens = ['token' => [
            '--color--primary' => '#112233',
            '--color--palette-1' => '#abcdef',
            '--color--palette-1-contrast' => '#123456',
            '--color--palette-2-contrast' => '#888888',
        ], '_dark' => ['--color--primary' => '#ffffff']];
        $result = $seed->populate($tokens, ['#112233' => 100, '#ffffff' => 5, '#000000' => 10, '#abcdef' => 15]);
        static::assertSame('#abcdef', $result['token']['--color--palette-1']);
        static::assertSame('#123456', $result['token']['--color--palette-1-contrast']);
        static::assertArrayNotHasKey('--color--palette-2', $result['token']);
        static::assertSame('#000000', $result['token']['--color--palette-3']);
        static::assertSame('#ffffff', $result['token']['--color--palette-3-contrast']);
        static::assertSame('#ffffff', $result['token']['--color--palette-4']);
        static::assertSame('#000000', $result['token']['--color--palette-4-contrast']);
        static::assertSame($tokens['_dark'], $result['_dark']);
        static::assertSame($result, $seed->populate($result, ['#000000' => 10, '#ffffff' => 5]));
    }

    public function testReturnsImmediatelyAfterTheOneTimeSeed(): void
    {
        $wp = new FakeWpService(['getOption' => 1]);
        (new PopulateUsedColors($wp, $this->createMock(GetField::class)))->run();
        static::assertArrayNotHasKey('getPosts', $wp->methodCalls);
        static::assertArrayNotHasKey('setThemeMod', $wp->methodCalls);
    }

    public function testGroupsSimilarColorsBeforeRankingAndAvoidsExistingColors(): void
    {
        $seed = new PopulateUsedColors(new FakeWpService(), $this->createMock(GetField::class));
        $tokens = ['token' => ['--color--primary' => '#aabbcc']];
        $result = $seed->populate($tokens, [
            '#ff0000' => 6,
            '#00aa00' => 4,
            '#01ab01' => 4,
            '#abbccd' => 100,
        ]);
        static::assertSame('#00aa00', $result['token']['--color--palette-1']);
        static::assertSame('#ff0000', $result['token']['--color--palette-2']);
        static::assertArrayNotHasKey('--color--palette-3', $result['token']);
    }

    public function testRunCountsModuleColorsBeforeMarkingTheSeedComplete(): void
    {
        $wp = new FakeWpService([
            'getOption' => false,
            'getThemeMod' => '{}',
            'getPostTypes' => [],
            'getPosts' => static fn(array $args): array => is_array($args['post_type']) ? [7, 8] : [9],
            'getPostMeta' => static fn(int $id): string => $id === 7 ? '#2468aa' : '#2469ab',
            'setThemeMod' => true,
            'updateOption' => true,
        ]);
        $acf = $this->createMock(GetField::class);
        $acf->method('getField')->willReturn([['custom_background_color' => '#ff0000']]);
        (new PopulateUsedColors($wp, $acf))->run();
        $saved = json_decode($wp->methodCalls['setThemeMod'][0][1], true, flags: JSON_THROW_ON_ERROR);
        static::assertSame('#2468aa', $saved['token']['--color--palette-1']);
        static::assertSame('#ff0000', $saved['token']['--color--palette-2']);
        static::assertSame(['municipio_used_color_palette_seeded', 1, false], $wp->methodCalls['updateOption'][0]);
    }
}
