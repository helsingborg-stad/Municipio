<?php

namespace Municipio\SchemaData\ExternalContent\JsonToSchemaObjects\SchemaSanitizer\Mappers;

use Municipio\Schema\BaseType;
use Municipio\Schema\Event;

class SetEventDatesFromEventSchedule implements MapperInterface {

    public function map(BaseType $schema): BaseType
    {
        if (!$schema instanceof Event) {
            return $schema;
        }

        
        if (!is_array($schema->getProperty('eventSchedule')) || empty($schema->getProperty('eventSchedule'))) {
            return $schema;
        }

        $schedule = (new FindClosestInArrayOfSchedules\FindClosestInArrayOfSchedules())->find(...$schema->getProperty('eventSchedule'));

        if ($schedule !== null) {

            $startDate = $this->ensureDateTime($schedule->getProperty('startDate'));
            $endDate = $this->ensureDateTime($schedule->getProperty('endDate'));

            if ($startDate !== null) {
                $schema->startDate($startDate);
            }

            if ($endDate !== null) {
                $schema->endDate($endDate);
            }
        }

        return $schema;
    }

    private function ensureDateTime(mixed $value): ?\DateTimeInterface
    {
        if ($value instanceof \DateTimeInterface) {
            return $value;
        }

        if (is_string($value)) {
            try {
                return new \DateTimeImmutable($value);
            } catch (\Exception $e) {
                return null;
            }
        }

        return null;
    }
}