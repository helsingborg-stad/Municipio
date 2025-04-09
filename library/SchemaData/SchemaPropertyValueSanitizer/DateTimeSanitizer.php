<?php

namespace Municipio\SchemaData\SchemaPropertyValueSanitizer;

use DateTime;

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

    private function sanitizeDate($value): DateTime|string|null
    {
        if (is_string($value)) {
            $parsedDate = date_parse($value);

            if ($parsedDate['year'] && $parsedDate['hour']) {
                return new \DateTime($value);
            } elseif ($parsedDate['year']) {
                return (new \DateTime($value))->format('Y-m-d');
            }
        }

        return $value;
    }
}
