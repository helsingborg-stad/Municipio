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
            $schema->startDate($schedule->getProperty('startDate'));
            $schema->endDate($schedule->getProperty('endDate'));
        }

        return $schema;
    }
}