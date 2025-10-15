<?php

namespace Municipio\ImageFocus\Resolver;

use Municipio\ImageFocus\Detector\FocalPointDetectorInterface;
use Imagick;
use \FreshleafMedia\Autofocus\FocalPointDetector;

class FocalPointDetectorResolver implements FocusPointResolverInterface
{
    public function __construct(private FocalPointDetectorInterface $detector) {}

    public function resolve(string $filePath, int $width, int $height, ?int $attachmentId = null): ?array
    {
        $focus = $this->detector->getPoint(new Imagick($filePath));

        if ($focus === null) {
            return null;
        }

        return [
            'left' => ($focus->x / $width) * 100,
            'top'  => ($focus->y / $height) * 100,
        ];
    }
}