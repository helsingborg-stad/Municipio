<?php

namespace Municipio\Controller\SingularEvent\Mappers;

use DateTime;
use Municipio\Controller\SingularEvent\Mappers\Occasion\OccasionInterface;
use Municipio\Schema\Event;

class MapCurrentOccasion implements EventDataMapperInterface
{
    /* @var OccasionInterface[] */
    private array $occasions;

    public function __construct(OccasionInterface ...$occasions)
    {
        $this->occasions = $occasions;
    }

    public function map(Event $event): ?OccasionInterface
    {
        foreach ($this->occasions as $occasion) {
            if ($occasion->isCurrent()) {
                return $occasion;
            }
        }

        return null;
    }
}
