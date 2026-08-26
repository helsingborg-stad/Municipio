<?php

namespace Municipio\Controller\School;

use Municipio\Helper\EnsureArrayOf\EnsureArrayOf;
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
            return $this->getPreambleFromDescriptionArray(...EnsureArrayOf::ensureArrayOf($description, TextObject::class));
        }

        return null;
    }

    private function getPreambleFromDescriptionArray(TextObject ...$description): ?string
    {
        foreach ($description as $textObject) {
            if ($textObject->getProperty('name') === 'role:preamble') {
                return $textObject->getProperty('text');
            }
        }

        return null;
    }
}
