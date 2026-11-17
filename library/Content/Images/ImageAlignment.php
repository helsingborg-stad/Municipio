<?php

namespace Municipio\Content\Images;

/**
 * Resolves image alignment from the classic editor (alignleft, alignright, aligncenter, alignnone)
 * and maps it to styleguide utility classes.
 *
 * Left/right aligned images float so that the following text wraps around them.
 * On the smallest breakpoint the float and side margin are removed and the image takes full width.
 */
class ImageAlignment
{
    public const LEFT   = 'left';
    public const RIGHT  = 'right';
    public const CENTER = 'center';
    public const NONE   = 'none';

    /**
     * WordPress alignment class => styleguide utility classes.
     */
    private const CLASS_MAP = [
        'alignleft'   => 'u-float--left u-float--none@xs u-margin__right--3 u-margin__right--0@xs u-margin__bottom--2 u-width--100@xs',
        'alignright'  => 'u-float--right u-float--none@xs u-margin__left--3 u-margin__left--0@xs u-margin__bottom--2 u-width--100@xs',
        'alignnone'   => '',
        'aligncenter' => 'u-margin__y--2 u-margin__x--auto u-text-align--center',
    ];

    /**
     * Classes that identify an alignment, both before and after replaceBuiltinClasses() has run.
     */
    private const ALIGNMENT_MARKERS = [
        self::LEFT   => ['alignleft', 'u-float--left'],
        self::RIGHT  => ['alignright', 'u-float--right'],
        self::CENTER => ['aligncenter', 'u-margin__x--auto'],
        self::NONE   => ['alignnone'],
    ];

    /**
     * Replace WordPress alignment classes in a string of HTML with utility classes.
     *
     * @param string $content
     * @return string
     */
    public static function replaceAlignmentClasses(string $content): string
    {
        return str_replace(array_keys(self::CLASS_MAP), array_values(self::CLASS_MAP), $content);
    }

    /**
     * Get the alignment described by a list of classes.
     *
     * @param array $classes
     * @return string|null One of the class constants, or null if no alignment is found.
     */
    public static function getAlignment(array $classes): ?string
    {
        foreach (self::ALIGNMENT_MARKERS as $alignment => $markers) {
            if (!empty(array_intersect($markers, $classes))) {
                return $alignment;
            }
        }

        return null;
    }

    /**
     * Get the utility classes for an alignment.
     *
     * @param string $alignment One of the class constants.
     * @return array
     */
    public static function getClassesForAlignment(string $alignment): array
    {
        $classString = self::CLASS_MAP['align' . $alignment] ?? '';

        return array_values(array_filter(explode(' ', $classString)));
    }

    /**
     * Resolve the class list for a rendered image.
     *
     * In the classic editor, images without a caption carry the alignment class on the
     * <img> itself and not on a wrapping element. When the wrapper has no alignment,
     * the alignment of the image is moved to the wrapper so that it is not lost.
     *
     * @param array $wrapperClasses Classes of the element wrapping the image (figure, p, a).
     * @param array $imageClasses   Classes of the <img> element.
     * @return array
     */
    public static function resolveClassList(array $wrapperClasses, array $imageClasses): array
    {
        $wrapperClasses = array_values(array_filter($wrapperClasses));

        if (self::getAlignment($wrapperClasses) !== null) {
            return $wrapperClasses;
        }

        $imageAlignment = self::getAlignment(array_filter($imageClasses));

        if ($imageAlignment === null) {
            return $wrapperClasses;
        }

        return array_values(array_unique(array_merge(
            $wrapperClasses,
            self::getClassesForAlignment($imageAlignment)
        )));
    }

    /**
     * Split a class attribute into a list of classes.
     *
     * @param string|null $classAttribute
     * @return array
     */
    public static function splitClassAttribute(?string $classAttribute): array
    {
        return array_values(array_filter(preg_split('/\s+/', (string) $classAttribute) ?: []));
    }
}
