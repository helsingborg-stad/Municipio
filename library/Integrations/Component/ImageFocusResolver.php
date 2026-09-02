<?php

namespace Municipio\Integrations\Component;

use ComponentLibrary\Integrations\Image\ImageFocusResolverInterface;

class ImageFocusResolver implements ImageFocusResolverInterface
{
    private static array $runtimeCache = [];

  /**
   * Constructor
   *
   * @param array $data The data array to resolve from
   */
    public function __construct(private $data)
    {
    }

  /**
   * Get focus point
   *
   * @return array
   */
    public function getFocusPoint(): array {
        $data = $this->data;
        $focusPoint = [
            'left' => 50,
            'top' => 50
        ];
    
        if ($data && isset($data['left'], $data['top'])) {
            $focusPoint['left'] = $data['left'] ?? 50;
            $focusPoint['top'] = $data['top'] ?? 50;
        }

        if (!empty($data['id']) && $focusPoint['left'] === 50 && $focusPoint['top'] === 50) {
            $cacheKey = $this->getCacheKey($data);

            if (array_key_exists($cacheKey, self::$runtimeCache)) {
                return self::$runtimeCache[$cacheKey];
            }

            $focusPoint = apply_filters('attachment_focus_point', $focusPoint, $data['id']);
            self::$runtimeCache[$cacheKey] = $focusPoint;
        }

        return $focusPoint;
    }

    /**
     * Cache focus points for the same attachment and aspect ratio during a request.
     */
    private function getCacheKey(array $data): string
    {
        $blogId = function_exists('get_current_blog_id') ? get_current_blog_id() : 0;

        return implode(':', [
            $blogId,
            (int) $data['id'],
            $this->normalizeRatio($data['ratio'] ?? null)
        ]);
    }

    private function normalizeRatio($ratio): string
    {
        if (
            !is_array($ratio) ||
            !isset($ratio[0], $ratio[1]) ||
            !is_numeric($ratio[0]) ||
            !is_numeric($ratio[1]) ||
            (int) $ratio[0] <= 0 ||
            (int) $ratio[1] <= 0
        ) {
            return 'intrinsic';
        }

        $width = (int) round($ratio[0]);
        $height = (int) round($ratio[1]);
        $divisor = $this->greatestCommonDivisor($width, $height);

        return ($width / $divisor) . ':' . ($height / $divisor);
    }

    private function greatestCommonDivisor(int $first, int $second): int
    {
        while ($second !== 0) {
            [$first, $second] = [$second, $first % $second];
        }

        return $first;
    }
}
