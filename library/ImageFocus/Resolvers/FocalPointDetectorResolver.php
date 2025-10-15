<?php

namespace Municipio\ImageFocus\Resolvers;

use Imagick;

class FocalPointDetectorResolver implements FocusPointResolverInterface
{
    public function __construct(private $detector) {}

    public function isSupported(): bool
    {
        return class_exists(\FreshleafMedia\Autofocus\FocalPointDetector::class) && extension_loaded('imagick');
    }

    public function resolve(string $filePath, int $width, int $height, ?int $attachmentId = null): ?array
    {
        $focusPoint = $this->detector->getPoint(new Imagick($filePath));

        if (!isset($focusPoint->x) || !isset($focusPoint->y)) {
            return null;
        }

        return $this->pixelToPercent($focusPoint, $width, $height);
    }

    private function pixelToPercent(\stdClass $focusPoint, int $width, int $height): array
    { 
        return [
            'left' => ($focusPoint->x / $width) * 100,
            'top'  => ($focusPoint->y / $height) * 100,
        ];
    }
}