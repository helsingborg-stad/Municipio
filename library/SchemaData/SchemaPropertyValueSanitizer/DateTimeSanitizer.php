<?php

namespace Municipio\SchemaData\SchemaPropertyValueSanitizer;

use DateTime;

/**
 * DateTimeSanitizer.
 *
 * Sanitizes DateTime values.
 */
class DateTimeSanitizer implements SchemaPropertyValueSanitizerInterface
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
        if (in_array('\DateTimeInterface', $allowedTypes)) {
            return $this->sanitizeDate($value);
        }

        if (in_array('\DateTimeInterface[]', $allowedTypes)) {
            return array_map(fn ($date) => $this->sanitizeDate($date), $value);
        }

        return $this->inner->sanitize($value, $allowedTypes);
    }

    /**
     * Sanitize the date value.
     *
     * @param mixed $value The value to sanitize.
     * @return DateTime|string|null The sanitized date value.
     */
    private function sanitizeDate($value): DateTime|string|null
    {
        if (is_string($value)) {
            $dateTime = date_create($value);

            if ($dateTime) {
                return $dateTime;
            }
        }

        return $value;
    }
}
