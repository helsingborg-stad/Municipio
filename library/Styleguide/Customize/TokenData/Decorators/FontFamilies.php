<?php

namespace Municipio\Styleguide\Customize\TokenData\Decorators;

use WpService\Contracts\ApplyFilters;

class FontFamilies implements DecoratorInterface
{
    public function __construct(
        private ApplyFilters $wpService,
    ) {}

    public function decorate(array $tokenData): array
    {
        $tagetVariables = ['--font-family-base', '--font-family-heading'];

        foreach ($tokenData['categories'] as &$category) {
            foreach ($category['settings'] as &$setting) {
                if (in_array($setting['variable'], $tagetVariables, true)) {
                    $setting = $this->modifySetting($setting);
                }
            }
        }

        return $tokenData;
    }

    private function modifySetting(array $setting): array
    {
        $setting = [
            ...$setting,
            'type' => 'select',
            'options' => $this->getFontOptions($setting),
        ];

        return $setting;
    }

    private function getFontOptions(array $setting): array
    {
        $options = [];

        if (!empty($setting['default']) && is_string($setting['default'])) {
            $options[] = [
                'value' => $setting['default'],
                'label' => $setting['default'],
            ];
        }

        return $this->wpService->applyFilters('Municipio/Styleguide/Customize/TokenData/FontFamilies', array_merge($options, [
            ['value' => 'Arial, sans-serif', 'label' => 'Arial'],
            ['value' => 'Helvetica, sans-serif', 'label' => 'Helvetica'],
            ['value' => 'sans-serif', 'label' => 'sans-serif'],
        ]));
    }
}
