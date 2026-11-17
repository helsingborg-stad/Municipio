<?php

declare(strict_types=1);

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
     * Class added to images rendered from post content. Their width comes from the
     * --municipio-content-image-width custom property, see assets/source/sass/image.scss.
     */
    public const CONTENT_IMAGE_CLASS = 'municipio-content-image';

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
            if (array_intersect($markers, $classes) !== []) {
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
     * Resolve the class list for the image component that replaces an image in the content.
     *
     * The result is final: WordPress classes (alignment, wp-caption, wp-image-*, size-*)
     * are replaced by utility classes, so the list does not depend on
     * replaceBuiltinClasses() running on the rendered HTML afterwards.
     *
     * In the classic editor, images without a caption carry the alignment class on the
     * <img> itself and not on a wrapping element. The alignment of the wrapper is used
     * when present, otherwise the alignment of the image.
     *
     * @param array $wrapperClasses Classes of the element wrapping the image (figure, p, a).
     * @param array $imageClasses   Classes of the <img> element.
     * @return array
     */
    public static function resolveClassList(array $wrapperClasses, array $imageClasses): array
    {
        $wrapperClasses = array_values(array_filter($wrapperClasses));
        $alignment      = self::getAlignment($wrapperClasses) ?? self::getAlignment(array_filter($imageClasses));

        $classes = array_filter(
            $wrapperClasses,
            static fn (string $class): bool => !self::isBuiltinClass($class)
        );

        if ($alignment !== null) {
            $classes = array_merge($classes, self::getClassesForAlignment($alignment));
        }

        return array_values(array_unique($classes));
    }

    /**
     * Whether a class is a WordPress image class, or an alignment utility class, that
     * resolveClassList() replaces.
     *
     * @param string $class
     * @return bool
     */
    private static function isBuiltinClass(string $class): bool
    {
        static $alignmentClasses = null;

        $alignmentClasses ??= array_merge(
            array_keys(self::CLASS_MAP),
            ...array_map(static fn (string $classes): array => explode(' ', $classes), array_values(self::CLASS_MAP))
        );

        return $class === 'wp-caption'
            || str_starts_with($class, 'wp-image-')
            || str_starts_with($class, 'size-')
            || in_array($class, $alignmentClasses, true);
    }

    /**
     * Split a class attribute into a list of classes.
     *
     * @param string|null $classAttribute
     * @return array
     */
    public static function splitClassAttribute(?string $classAttribute): array
    {
        $classes = preg_split('/\s+/', (string) $classAttribute);

        return $classes === false ? [] : array_values(array_filter($classes));
    }
}
