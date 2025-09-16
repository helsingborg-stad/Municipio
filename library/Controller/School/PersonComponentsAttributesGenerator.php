<?php

namespace Municipio\Controller\School;

use Municipio\Schema\ElementarySchool;
use Municipio\Schema\Preschool;
use Municipio\Schema\ImageObject;
use Municipio\Schema\Person;

class PersonComponentsAttributesGenerator
{
    public function __construct(private ElementarySchool|Preschool $school)
    {
    }

    public function generate(): mixed
    {
        return $this->getPersonsAttributes($this->school->getProperty('employee'));
    }

    private function getPersonsAttributes(array|null|Person $employees): ?array
    {
        if (empty($employees)) {
            return null;
        }

        $persons = is_a($employees, Person::class) ? [$employees] : array_filter($employees, fn($item) => is_a($item, Person::class));

        return array_map(fn($person) => $this->getAttributesFromPerson($person), $persons);
    }

    private function getAttributesFromPerson(Person $person): array
    {
        return [
            'givenName' => $person->getProperty('name'),
            'jobTitle'  => $person->getProperty('jobTitle') ?? '',
            'telephone' => [['number' => $person->getProperty('telephone')]],
            'email'     => $person->getProperty('email') ?? '',
            'image'     => is_a($person->getProperty('image'), ImageObject::class) ? $person->getProperty('image')->getProperty('url') : null,
        ];
    }
}
