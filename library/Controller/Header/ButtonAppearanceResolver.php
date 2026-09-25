<?php

namespace Municipio\Controller\Header;

use Municipio\Controller\Header\Helper\GetHiddenData;

/**
 * Resolves button appearance settings for header items.
 */
class ButtonAppearanceResolver
{
    /**
     * Constructor.
     */
    public function __construct(
        private object $customizer,
        private GetHiddenData $getHiddenData,
    ) {
    }

    /**
     * Resolve per-item button appearance settings.
     *
     * @param array<string, mixed> $items Header items.
     * @param string $setting Header sortable setting name.
     * @param array<string, string> $defaultAppearance Default button appearance.
     *
     * @return array<string, array<string, string>>
     */
    public function resolve(array $items, string $setting, array $defaultAppearance): array
    {
        $appearance = [];
        $responsiveSetting = $setting . '_responsive';
        $desktopItems = $items['desktop'] ?? [];
        $mobileItems = $items['mobile'] ?? [];

        foreach (array_unique(array_merge(array_keys($desktopItems), array_keys($mobileItems))) as $menu) {
            $sourceSetting = !isset($desktopItems[$menu]) && isset($mobileItems[$menu])
                ? $responsiveSetting
                : $setting;
            $itemSettings = $this->getHiddenData->get()->{$sourceSetting}->{$menu} ?? (object) [];

            $appearance[$menu] = [
                'style' => $itemSettings->buttonStyle ?? $defaultAppearance['style'],
                'size' => $itemSettings->buttonSize ?? $defaultAppearance['size'],
                'color' => $itemSettings->buttonColor ?? $defaultAppearance['color'],
            ];
        }

        return $appearance;
    }

    /**
     * Get the default header button appearance from the customizer.
     *
     * @return array<string, string>
     */
    public function getDefaultAppearance(): array
    {
        return [
            'style' => $this->customizer->headerTriggerButtonType ?? 'basic',
            'size' => $this->customizer->headerTriggerButtonSize ?? 'md',
            'color' => $this->customizer->headerTriggerButtonColor ?? 'inherit',
        ];
    }
}