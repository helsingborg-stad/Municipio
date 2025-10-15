<?php

namespace Municipio\ImageFocus\Resolvers;

use Imagick;

class FocalPointDetectorResolver implements FocusPointResolverInterface
{
    public function __construct(private $detector) {}

    public function resolve(string $filePath, int $width, int $height, ?int $attachmentId = null): ?array
    {
        $focus = $this->detector->getPoint(new Imagick($filePath));

        if ($focus === null) {
            return null;
        }

        return $this->pixelToPercent($focus, $width, $height);
    }

    private function pixelToPercent(\stdClass $focusPoint, int $width, int $height): array
    { 
        return [
            'left' => ($focusPoint->x / $width) * 100,
            'top'  => ($focusPoint->y / $height) * 100,
        ];
    }
}