<?php

namespace Municipio\PostObject\Icon;

/**
 * Interface that adhere to the Icon component atttibutes.
 * @see https://styleguide.getmunicipio.com/components/atoms/icon
 */
interface IconInterface
{
    /**
     * Sizes: xs, sm, md, lg, xl, xxl
     */
    public function getSize(): string;

    /**
     * A label on the icon
     */
    public function getLabel(): string;

    /**
     * Get the term icon.
     */
    public function getIcon(): string;

    /**
     * Get the term color.
     */
    public function getColor(): string;

    /**
     * A custom HEX color
     */
    public function getCustomColor(): string;

    /**
     *  Icon HTML tag
     */
    public function getComponentElement(): string;

    /**
     * If the icons should be filled or not
     */
    public function getFilled(): bool;

    /**
     * If the icon is decorative only or serves a purpose.
     */
    public function getDecorative(): bool;

    /**
     * Get the term ID.
     */
    public function toArray(): array;
}
