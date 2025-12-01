<?php

namespace Municipio\SchemaData\SchemaPropertyValueSanitizer;

use Municipio\Schema\Schema;

/**
 * Class ArrayOfImagesSanitizer
 *
 * Sanitizes an array of images to ensure they are valid ImageObject schemas.
 */
class ArrayOfImagesSanitizer implements SchemaPropertyValueSanitizerInterface
{
    /**
     * Class constructor.
     *
     * @param mixed $inner The inner sanitizer.
     */
    public function __construct(private $inner = new NullSanitizer())
    {
    }

    /**
     * @inheritDoc
     */
    public function sanitize(mixed $value, array $allowedTypes): mixed
    {
        if (in_array('ImageObject[]', $allowedTypes) && is_array($value)) {
            return array_map(fn($image) => $this->sanitizeImageObject($image), $value);
        }

        return $this->inner->sanitize($value, $allowedTypes);
    }

    /**
     * Sanitizes an image object to ensure it is a valid ImageObject schema.
     *
     * @param mixed $image The image object to sanitize.
     *
     * @return mixed The sanitized image object.
     */
    private function sanitizeImageObject($image): mixed
    {
        if (is_array($image) && $image['@type'] === 'ImageObject') {
            $imageSchema = Schema::imageObject()
                ->setProperty('@id', $image['@id'] ?? null)
                ->url($image['url'] ?? null)
                ->caption($image['caption'] ?? null)
                ->description($image['description'] ?? null)
                ->name($image['name'] ?? null);
            return $imageSchema;
        }

        return $image;
    }
}
