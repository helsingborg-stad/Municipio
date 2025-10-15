<?php

namespace Municipio\ImageFocus\Resolvers;

use Astrotomic\DeepFace\DeepFace;
use Astrotomic\DeepFace\Enums\Detector;
use Error;
use Municipio\ImageFocus\Resolvers\FocusPointResolverInterface;

/**
 * Resolver that uses DeepFace to detect faces in an image and determine a focus point.
 * 
 * Requirements:
 * - Composer: composer require astrotomic/php-deepface
 * - Python: pip install deepface, pip install tf-keras
 * - Libs: sudo apt install -y libgl1-mesa-glx libglib2.0-0
 * 
 * DeepFace should be callable via CLI from PHP (ensure Python's deepface is in PATH).
 */
class FaceDetectingFocusPointResolver implements FocusPointResolverInterface
{
    private DeepFace $deepFace;

    public function __construct()
    {
        $this->deepFace = new DeepFace();

        // Set cache directory for DeepFace models
        putenv('DEEPFACE_HOME=/tmp/deepface_cache');
    }

    public function isSupported(): bool
    {
        return class_exists(DeepFace::class);
    }

    public function resolve(string $filePath, int $width, int $height, ?int $attachmentId = null): ?array
    {
        try {
            $faces = $this->deepFace->extractFaces($filePath, enforce_detection: true);

            if (empty($faces)) {
                return null;
            }

            $xs = [];
            $ys = [];

            foreach ($faces as $face) {
                if (!isset($face->facial_area) || !is_object($face->facial_area)) {
                    continue;
                }
                $area = $face->facial_area;
                // Defensive: check if x, y, w, h are set
                if (
                    !isset($area->x, $area->y, $area->w, $area->h) ||
                    !is_numeric($area->x) || !is_numeric($area->y) ||
                    !is_numeric($area->w) || !is_numeric($area->h)
                ) {
                    continue;
                }
                $xs[] = $area->x + ($area->w / 2);
                $ys[] = $area->y + ($area->h / 2);
            }

            if (empty($xs) || empty($ys)) {
                return null;
            }

            // Average all face centers (handles multiple faces)
            $x = array_sum($xs) / count($xs);
            $y = array_sum($ys) / count($ys);

            // Success
            error_log('[ImageFocus][FaceDetectingFocusPointResolver]: Successfully resolved focus point.');

            return [
                'left' => ($x / $width) * 100,
                'top'  => ($y / $height) * 100,
            ];

        } catch (\Throwable $e) {
            error_log('[ImageFocus][FaceDetectingFocusPointResolver]: ' . $e->getMessage());
            return null;
        }
    }
}