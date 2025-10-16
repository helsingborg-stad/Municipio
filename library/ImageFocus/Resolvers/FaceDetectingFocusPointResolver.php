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
 * - Python: pip install deepface tf-keras numpy pandas tensorflow
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
        $tmpFile    = $this->createTmpFile($filePath);
        if($tmpFile === null) {
            return null;
        }
        $isTemp     = $tmpFile !== $filePath;
        $filePath   = $isTemp ? $tmpFile : $filePath;

        try {
            $faces = $this->deepFace->extractFaces($filePath);

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
            error_log('[ImageFocus][FaceDetectingFocusPointResolver]: Detected ' . count($faces) . ' face(s).');
            return [
                'left' => ($x / $width) * 100,
                'top'  => ($y / $height) * 100,
            ];

        } catch (\Throwable $e) {
            error_log('[ImageFocus][FaceDetectingFocusPointResolver]: ' . $e->getMessage());
            return null;
        } finally {
            if ($isTemp && file_exists($filePath)) {
                unlink($filePath);
            }
        }
    }

    /**
     * If the file path is an S3 URI, download it to a temporary file and return the local path.
     * Otherwise, return the original path.
     *
     * @param string $filePath
     * @return string|null Local file path or null on failure
     */
    private function createTmpFile(string $filePath): ?string
    {
        if (str_starts_with($filePath, 's3://')) {
            $tempFile = tempnam(sys_get_temp_dir(), 'imgfocus_');
            if ($tempFile === false) {
                error_log('[ImageFocus][FaceDetectingFocusPointResolver]: Failed to create temp file.');
                return null;
            }

            $stream = fopen($filePath, 'rb');
            if ($stream === false) {
                error_log('[ImageFocus][FaceDetectingFocusPointResolver]: Failed to open S3 stream.');
                unlink($tempFile);
                return null;
            }

            $bytesWritten = file_put_contents($tempFile, stream_get_contents($stream));
            fclose($stream);

            if ($bytesWritten === false) {
                error_log('[ImageFocus][FaceDetectingFocusPointResolver]: Failed to write S3 file to temp file.');
                unlink($tempFile);
                return null;
            }

            return $tempFile;
        }

        return $filePath;
    }
}