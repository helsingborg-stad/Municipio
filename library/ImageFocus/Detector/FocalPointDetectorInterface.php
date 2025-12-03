<?php

namespace Municipio\ImageFocus\Detector;

use Imagick;

interface FocalPointDetectorInterface
{
    /**
     * Detect a focal point from an image.
     * @return object|null Example: (object)['x' => float, 'y' => float]
     */
    public function getPoint(Imagick $image): ?object;
}