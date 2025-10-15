<?php

namespace Municipio\ImageFocus\Resolvers;

use Astrotomic\DeepFace\DeepFace;
use Municipio\ImageFocus\Resolvers\FocusPointResolverInterface;

/**
 * Resolver that uses DeepFace to detect faces in an image and determine a focus point.
 * Requires the "astrotomic/deep-face" package, and the deepface python framework 
 * to be installed and configured on the server.
 * 
 * Installation:
 * pip install deepface
 */
class FaceDetectingFocusPointResolver implements FocusPointResolverInterface
{
    public function isSupported(): bool
    {
        return class_exists(DeepFace::class);
    }

    public function resolve(string $filePath, int $width, int $height, ?int $attachmentId = null): ?array
    {
        try {
            $faces = DeepFace::detect($filePath);
        } catch (\Exception $e) {
            return null;
        }

        if (empty($faces)) {
            return null;
        }

        // Calculate the median focal point of all detected faces
        $xs = [];
        $ys = [];
        foreach ($faces as $face) {
            $xs[] = $face['x'] + ($face['w'] / 2);
            $ys[] = $face['y'] + ($face['h'] / 2);
        }
        $x = array_sum($xs) / count($xs);
        $y = array_sum($ys) / count($ys);

        return [
            'left' => ($x / $width) * 100,
            'top'  => ($y / $height) * 100,
        ];
    }
}