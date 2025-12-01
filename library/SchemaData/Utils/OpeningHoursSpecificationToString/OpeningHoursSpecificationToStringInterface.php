<?php

namespace Municipio\SchemaData\Utils\OpeningHoursSpecificationToString;

use Municipio\Schema\OpeningHoursSpecification;

interface OpeningHoursSpecificationToStringInterface
{
    /**
     * Convert OpeningHoursSpecification to array of string representations.
     *
     * @param OpeningHoursSpecification $openingHours
     * @return string[]
     */
    public function convert(OpeningHoursSpecification $openingHours): array;
}
