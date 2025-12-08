<?php

namespace Municipio\Controller\School;

use Municipio\Schema\ElementarySchool;
use Municipio\Schema\Preschool;
use Municipio\Schema\TextObject;

class PreambleGenerator
{
    public function __construct(private ElementarySchool|Preschool $school)
    {
    }

    public function generate(): mixed
    {
        return $this->getPreambleFromDescription($this->school->getProperty('description'));
    }

    private function getPreambleFromDescription(array|string|TextObject|null $description): ?string
    {
        if (is_array($description)) {
            return $this->getPreambleFromDescription($description[0]);
        }

        if (is_string($description)) {
            return $description;
        }

        if ($description instanceof TextObject && is_string($description->getProperty('text'))) {
            return $description->getProperty('text');
        }

        return null;
    }
}
