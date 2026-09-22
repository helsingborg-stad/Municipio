<?php

declare(strict_types=1);

namespace Municipio\Customizer;

use Municipio\Customizer\Sections\Menu\MegaMenu;
use PHPUnit\Framework\TestCase;

final class ButtonSettingsTest extends TestCase
{
    protected function setUp(): void
    {
        PanelsRegistry::getInstance()->fields = [];
    }

    public function testMegaMenuButtonVariantsUseSharedSettings(): void
    {
        new MegaMenu('municipio_customizer_section_mega_menu');

        $parentSize = $this->getField('mega_menu_item_button_size');
        $childSize = $this->getField('mega_menu_child_item_button_size');

        static::assertSame('md', $parentSize['default']);
        static::assertSame('parentSize', $parentSize['output'][0]['dataKey']);
        static::assertSame('sm', $childSize['default']);
        static::assertSame('childSize', $childSize['output'][0]['dataKey']);
    }

    /** @return array<string, mixed> */
    private function getField(string $settings): array
    {
        foreach (PanelsRegistry::getInstance()->getRegisteredFields() as $field) {
            if (($field['settings'] ?? null) === $settings) {
                return $field;
            }
        }

        self::fail(sprintf('Field %s was not registered.', $settings));
    }
}
