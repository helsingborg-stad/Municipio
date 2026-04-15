<?php

declare(strict_types=1);

namespace Municipio\Customizer\Fonts\Sections;

use Municipio\Customizer\Fonts\FontChoices;
use Municipio\Customizer\KirkiField;

/**
 * Registers header brand font controls.
 */
class HeaderBrandFont
{
    /**
     * @param string $sectionID
     */
    public function __construct(string $sectionID)
    {
        KirkiField::addField([
            'type' => 'typography',
            'settings' => 'header_brand_font_settings',
            'active_callback' => $this->getHeaderBrandEnabledActiveCallback(),
            'label' => __('Header Logotype Text: Font Settings ', 'municipio'),
            'section' => $sectionID,
            'priority' => 10,
            'choices' => FontChoices::getTypographyChoices(),
            'default' => [
                'font-size' => '2.25rem',
                'font-family' => 'Roboto',
                'variant' => '400',
                'line-height' => '1.2',
                'letter-spacing' => '0',
                'text-transform' => 'none',
            ],
            'output' => [
                [
                    'choice' => 'font-size',
                    'element' => ':root',
                    'property' => '--c-brand-font-size',
                ],
                [
                    'choice' => 'font-family',
                    'element' => '.c-brand .c-brand__text, .c-header__brand-text',
                    'property' => 'font-family',
                ],
                [
                    'choice' => 'variant',
                    'element' => '.c-brand .c-brand__text, .c-header__brand-text',
                    'property' => 'font-variant',
                ],
                [
                    'choice' => 'line-height',
                    'element' => '.c-brand .c-brand__text, .c-header__brand-text',
                    'property' => 'line-height',
                ],
                [
                    'choice' => 'letter-spacing',
                    'element' => '.c-brand .c-brand__text, .c-header__brand-text',
                    'property' => 'letter-spacing',
                ],
                [
                    'choice' => 'text-transform',
                    'element' => '.c-brand .c-brand__text, .c-header__brand-text',
                    'property' => 'text-transform',
                ],
            ],
        ]);
    }

    /**
     * Returns the active callback for the header brand toggle.
     *
     * @return array<int, array<string, bool|string>>
     */
    private function getHeaderBrandEnabledActiveCallback(): array
    {
        return [
            [
                'setting' => 'header_brand_enabled',
                'operator' => '==',
                'value' => true,
            ],
        ];
    }
}
