<?php

namespace Municipio\Controller\SingularPreschool;

use Municipio\Schema\Preschool;
use Municipio\Schema\ImageObject;
use Municipio\Schema\Person;

class PersonComponentsAttributesGenerator
{
    public function __construct(private Preschool $preschool)
    {
    }

    public function generate(): mixed
    {
        return $this->getPersonsAttributes($this->preschool->getProperty('employee'));
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
