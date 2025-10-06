<?php

namespace Municipio\SchemaData\ExternalContent\JsonToSchemaObjects\SchemaSanitizer\Mappers\FindClosestInArrayOfSchedules;

use Municipio\Schema\Schedule;

class FindClosestInArrayOfSchedules {
    /**
     * Finds the closest future schedule, or the closest past schedule if no future exists.
     * @param Schedule[] $schedules
     * @return Schedule|null
     */
    public function find(Schedule ...$schedules):?Schedule 
    {
        usort($schedules, fn (Schedule $a, Schedule $b) =>
            $a->getProperty('startDate') <=> $b->getProperty('startDate'));

        $now = new \DateTime();

        foreach ($schedules as $schedule) {
            if ($schedule->getProperty('startDate') >= $now) {
                return $schedule;
            }
        }

        foreach (array_reverse($schedules) as $schedule) {
            if ($schedule->getProperty('startDate') < $now) {
                return $schedule;
            }
        }

        return null;
    }
}