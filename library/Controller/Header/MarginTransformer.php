<?php

namespace Municipio\Controller\Header;

/**
 * MarginTransformer class.
 *
 * This class is responsible for transforming the margin data.
 */
class MarginTransformer {
    /**
     * Class MarginTransformer
     *
     * This class is responsible for transforming the margin data of the header.
     */
    public function __construct(private object $data)
    {}

    /**
     * Transforms the header menu items by applying margin classes based on the settings.
     *
     * @param array $items The header menu items.
     * @param mixed $settings The settings for applying margin classes.
     * @return array The transformed header menu items.
     */
    public function transform($items, $settings): array
    {
        if (empty($items['modified'])) {
            return $items;
        }

        foreach ($items['modified'] as $menu => &$classes) {
            if (!empty($this->data->{$settings}->{$menu}->margin)) {
                $classes = $this->getMarginClass($classes, $this->data->{$settings}->{$menu}->margin);
            }
        }

        return $items;
    }

    /**
     * Returns an array of margin classes based on the given margin value.
     *
     * @param array $classes The existing classes.
     * @param string $margin The margin value ('none', 'both', or any other value).
     * @return array The updated array of classes.
     */
    private function getMarginClass(array $classes, string $marginDirection)
    {
        $utility = 'u-margin__';
        $modifier = '--2';

        if ($marginDirection === 'none') {
        } elseif ($marginDirection === 'both') {
            $classes[] = $this->buildMarginUtility('left');
            $classes[] = $this->buildMarginUtility('right');
        } else {
            $classes[] = $this->buildMarginUtility($marginDirection);
        }

        return $classes;
    }

    /**
     * Builds a margin utility class based on the given margin direction.
     *
     * @param string $marginDirection The direction of the margin.
     * @return string The generated margin utility class.
     */
    private function buildMarginUtility(string $marginDirection)
    {
        return "u-margin__$marginDirection--2";
    }
}