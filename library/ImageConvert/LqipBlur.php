<?php

declare(strict_types=1);

namespace Municipio\ImageConvert;

use Municipio\HooksRegistrar\Hookable;
use WpService\Contracts\AddFilter;

/**
 * Applies a light server-side blur to the tiny LQIP renditions.
 *
 * The blur removes fine detail before encoding, which keeps the already-small
 * placeholders compact while the component's CSS blur handles presentation.
 */
class LqipBlur implements Hookable
{
    private const WIDTH = 15;

    public function __construct(private AddFilter $wpService)
    {
    }

    public function addHooks(): void
    {
        $this->wpService->addFilter('image_editor_save_pre', [$this, 'blur'], 10, 3);
    }

    /**
     * Blur LQIP image data immediately before WordPress writes it to disk.
     *
     * @param mixed $image The native Imagick instance or GD image handle.
     * @param mixed $editor The WordPress image editor instance.
     * @param mixed $filename The generated image filename.
     * @return mixed
     */
    public function blur(mixed $image, mixed $editor, mixed $filename): mixed
    {
        if (!is_string($filename) || !$this->isLqipFilename($filename)) {
            return $image;
        }

        if ($image instanceof \Imagick) {
            $image->gaussianBlurImage(0, 0.5);
            return $image;
        }

        if (
            function_exists('imagefilter')
            && defined('IMG_FILTER_GAUSSIAN_BLUR')
            && (is_resource($image) || $image instanceof \GdImage)
        ) {
            imagefilter($image, IMG_FILTER_GAUSSIAN_BLUR);
        }

        return $image;
    }

    private function isLqipFilename(string $filename): bool
    {
        return preg_match(
            sprintf('/-%dx\\d+(?:\\.tmp-[^.]+)?\\.[^.]+$/', self::WIDTH),
            $filename,
        ) === 1;
    }
}
