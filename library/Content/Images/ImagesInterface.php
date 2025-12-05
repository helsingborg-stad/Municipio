<?php

namespace Municipio\Content\Images;

interface ImagesInterface
{
    public function normalizeImages($content);
    public function imageHasBeenNormalized($image, $domImage);
}