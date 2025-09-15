<?php

declare(strict_types=1);

namespace Municipio\Controller\SingularElementarySchool;

use PHPUnit\Framework\TestCase;

class PersonComponentsAttributesGeneratorTest extends TestCase
{
    public function testCanBeInstantiated(): void
    {
        $elementarySchool = \Municipio\Schema\Schema::elementarySchool();
        $generator        = new PersonComponentsAttributesGenerator($elementarySchool);
        $this->assertInstanceOf(PersonComponentsAttributesGenerator::class, $generator);
    }

    public function testGenerateReturnsNullIfEmployeeIsEmpty(): void
    {
        $elementarySchool = \Municipio\Schema\Schema::elementarySchool()->employee(null);
        $generator        = new PersonComponentsAttributesGenerator($elementarySchool);
        $this->assertNull($generator->generate());
    }

    public function testGenerateReturnsAttributesForSinglePerson(): void
    {
        $person           = \Municipio\Schema\Schema::person()
            ->name('Test Name')
            ->jobTitle('Teacher')
            ->telephone('123456')
            ->email('test@example.com')
            ->image(null);
        $elementarySchool = \Municipio\Schema\Schema::elementarySchool()->employee($person);
        $generator        = new PersonComponentsAttributesGenerator($elementarySchool);
        $result           = $generator->generate();
        $this->assertEquals([
            [
                'givenName' => 'Test Name',
                'jobTitle'  => 'Teacher',
                'telephone' => [['number' => '123456']],
                'email'     => 'test@example.com',
                'image'     => null,
            ]
        ], $result);
    }

    public function testGenerateReturnsAttributesForMultiplePersons(): void
    {
        $person1          = \Municipio\Schema\Schema::person()
            ->name('Person One')
            ->jobTitle('Principal')
            ->telephone('111111')
            ->email('one@example.com')
            ->image(null);
        $person2          = \Municipio\Schema\Schema::person()
            ->name('Person Two')
            ->jobTitle('Counselor')
            ->telephone('222222')
            ->email('two@example.com')
            ->image(null);
        $elementarySchool = \Municipio\Schema\Schema::elementarySchool()->employee([$person1, $person2]);
        $generator        = new PersonComponentsAttributesGenerator($elementarySchool);
        $result           = $generator->generate();
        $this->assertEquals([
            [
                'givenName' => 'Person One',
                'jobTitle'  => 'Principal',
                'telephone' => [['number' => '111111']],
                'email'     => 'one@example.com',
                'image'     => null,
            ],
            [
                'givenName' => 'Person Two',
                'jobTitle'  => 'Counselor',
                'telephone' => [['number' => '222222']],
                'email'     => 'two@example.com',
                'image'     => null,
            ]
        ], $result);
    }
}
