<?php

declare(strict_types=1);

namespace Municipio\Customizer;

use Municipio\Customizer\Sections\Menu\MegaMenu;
use Municipio\Customizer\Sections\Menu\Tabmenu;
use PHPUnit\Framework\TestCase;

final class ButtonSettingsTest extends TestCase
{
    protected function setUp(): void
    {
        PanelsRegistry::getInstance()->fields = [];
    }

    public function testHeaderTriggerButtonsUseSharedButtonSettings(): void
    {
        new Tabmenu('municipio_customizer_section_header_panel_tab_menu');

        $style = $this->getField('header_trigger_button_type');
        $size = $this->getField('header_trigger_button_size');
        $color = $this->getField('header_trigger_button_color');

        static::assertSame(['filled', 'basic', 'outlined'], array_keys($style['choices']));
        static::assertSame(['sm', 'md', 'lg'], array_keys($size['choices']));
        static::assertSame(['inherit', 'primary', 'secondary'], array_keys($color['choices']));
        static::assertSame('inherit', $color['default']);
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