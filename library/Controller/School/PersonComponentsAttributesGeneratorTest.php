<?php

declare(strict_types=1);

namespace Municipio\Controller\School;

use PHPUnit\Framework\TestCase;

class PersonComponentsAttributesGeneratorTest extends TestCase
{
    public function testCanBeInstantiated(): void
    {
        $preschool = \Municipio\Schema\Schema::preschool();
        $generator = new PersonComponentsAttributesGenerator($preschool);
        $this->assertInstanceOf(PersonComponentsAttributesGenerator::class, $generator);
    }

    public function testGenerateReturnsNullIfEmployeeIsEmpty(): void
    {
        $preschool = \Municipio\Schema\Schema::preschool()->employee(null);
        $generator = new PersonComponentsAttributesGenerator($preschool);
        $this->assertNull($generator->generate());
    }

    public function testGenerateReturnsAttributesForSinglePerson(): void
    {
        $person    = \Municipio\Schema\Schema::person()
            ->name('Test Name')
            ->jobTitle('Teacher')
            ->telephone('123456')
            ->email('test@example.com')
            ->image(null);
        $preschool = \Municipio\Schema\Schema::preschool()->employee($person);
        $generator = new PersonComponentsAttributesGenerator($preschool);
        $result    = $generator->generate();
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
        $person1   = \Municipio\Schema\Schema::person()
            ->name('Person One')
            ->jobTitle('Principal')
            ->telephone('111111')
            ->email('one@example.com')
            ->image(null);
        $person2   = \Municipio\Schema\Schema::person()
            ->name('Person Two')
            ->jobTitle('Counselor')
            ->telephone('222222')
            ->email('two@example.com')
            ->image(null);
        $preschool = \Municipio\Schema\Schema::preschool()->employee([$person1, $person2]);
        $generator = new PersonComponentsAttributesGenerator($preschool);
        $result    = $generator->generate();
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
