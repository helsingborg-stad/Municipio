<?php

declare(strict_types=1);

namespace Municipio\Controller\Header\Helper;

use PHPUnit\Framework\TestCase;

class StickyResolverTest extends TestCase
{
    public function testReturnsFalseWhenStickyIsDisabled(): void
    {
        $resolver = $this->createResolver(
            (object) ['headerSticky' => ''],
            ['logotype'],
            ['primary']
        );

        static::assertFalse($resolver->isSticky(Enums::HEADER_KEY::UPPER->value));
        static::assertFalse($resolver->isSticky(Enums::HEADER_KEY::LOWER->value));
    }

    public function testPrefersLowerWhenBothHeadersHaveItems(): void
    {
        $resolver = $this->createResolver(
            (object) ['headerSticky' => 'sticky'],
            ['logotype'],
            ['primary']
        );

        static::assertFalse($resolver->isSticky(Enums::HEADER_KEY::UPPER->value));
        static::assertTrue($resolver->isSticky(Enums::HEADER_KEY::LOWER->value));
    }

    public function testUsesUpperWhenOnlyUpperHeaderHasItems(): void
    {
        $resolver = $this->createResolver(
            (object) ['headerSticky' => 'sticky'],
            ['logotype'],
            []
        );

        static::assertTrue($resolver->isSticky(Enums::HEADER_KEY::UPPER->value));
        static::assertFalse($resolver->isSticky(Enums::HEADER_KEY::LOWER->value));
    }

    public function testUsesLowerWhenOnlyLowerHeaderHasItems(): void
    {
        $resolver = $this->createResolver(
            (object) ['headerSticky' => 'sticky'],
            [],
            ['primary']
        );

        static::assertFalse($resolver->isSticky(Enums::HEADER_KEY::UPPER->value));
        static::assertTrue($resolver->isSticky(Enums::HEADER_KEY::LOWER->value));
    }

    private function createResolver(object $customizer, array $upperItems, array $lowerItems): StickyResolver
    {
        $customizer->headerSortableSectionMainUpper = json_encode($upperItems);
        $customizer->headerSortableSectionMainLower = json_encode($lowerItems);
        $customizer->headerSortableSectionMainUpperResponsive = json_encode([]);
        $customizer->headerSortableSectionMainLowerResponsive = json_encode([]);

        $customizer->headerSortableHiddenStorage = json_encode([
            'header_sortable_section_main_upper' => $this->buildConfigFor($upperItems),
            'header_sortable_section_main_lower' => $this->buildConfigFor($lowerItems),
            'header_sortable_section_main_upper_responsive' => [],
            'header_sortable_section_main_lower_responsive' => [],
        ]);

        return new StickyResolver(new ExtractMenuItems($customizer), $customizer);
    }

    private function buildConfigFor(array $items): array
    {
        $config = [];

        foreach ($items as $item) {
            $config[$item] = [
                'align' => 'left',
                'margin' => 'none',
            ];
        }

        return $config;
    }
}
