<?php

namespace Municipio\Helper;

use Municipio\Theme\Icon;

/**
 * Class Icons
 */
class Icons
{
    /**
     * Get icons with explicit, translated labels.
     *
     * @return array<int, array{name: string, label: string}> Icons with translated labels.
     */
    public static function getIcons(): array
    {
        $labels = Icon::getAltTexts();
        $icons  = [];

        foreach ($labels as $icon => $label) {
            $icons[] = [
                'name'  => $icon,
                'label' => $label,
            ];
        }

        return $icons;
    }
}
