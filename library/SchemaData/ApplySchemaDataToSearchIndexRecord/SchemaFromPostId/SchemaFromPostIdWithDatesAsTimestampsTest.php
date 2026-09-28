<?php

declare(strict_types=1);

namespace Municipio\SchemaData\ApplySchemaDataToSearchIndexRecord\SchemaFromPostId;

use Municipio\Schema\BaseType;
use Municipio\Schema\Schema;
use PHPUnit\Framework\Attributes\TestDox;

class SchemaFromPostIdWithDatesAsTimestampsTest extends \PHPUnit\Framework\TestCase
{
    #[TestDox('converts all DateTime instances to timestamps')]
    public function testConvertsAllDateTimeInstancesToTimestamps(): void
    {
        $inner = new class implements SchemaFromPostIdInterface {
            public function getSchema(int $postId): BaseType
            {
                return Schema::event()
                    ->startDate(new \DateTime('2024-01-01 12:00:00'))
                    ->endDate(new \DateTime('2024-01-01 13:00:00'));
            }
        };

        $sut = new SchemaFromPostIdWithDatesAsTimestamps($inner);

        $result = $sut->getSchema(1);

        $this->assertIsInt($result->getProperty('startDate'));
        $this->assertIsInt($result->getProperty('endDate'));
    }

    #[TestDox('converts nested DateTime instances to timestamps')]
    public function testConvertsNestedDateTimeInstancesToTimestamps(): void
    {
        $inner = new class implements SchemaFromPostIdInterface {
            public function getSchema(int $postId): BaseType
            {
                return Schema::event()
                    ->startDate(new \DateTime('2024-01-01 12:00:00'))
                    ->endDate(new \DateTime('2024-01-01 13:00:00'))
                    ->actor(Schema::person()->birthDate(new \DateTime('2000-01-01 00:00:00')));
            }
        };

        $sut = new SchemaFromPostIdWithDatesAsTimestamps($inner);

        $result = $sut->getSchema(1);

        $this->assertIsInt($result->getProperty('startDate'));
        $this->assertIsInt($result->getProperty('endDate'));
        $this->assertIsInt($result->getProperty('actor')['birthDate']);
    }

    #[TestDox('converts nested DateTime instances to timestamps in arrays')]
    public function testConvertsNestedDateTimeInstancesToTimestampsInArrays(): void
    {
        $inner = new class implements SchemaFromPostIdInterface {
            public function getSchema(int $postId): BaseType
            {
                return Schema::event()->eventSchedule([
                    Schema::event()
                        ->startDate(new \DateTime('2024-01-01 12:00:00'))
                        ->endDate(new \DateTime('2024-01-01 13:00:00')),
                ]);
            }
        };

        $sut = new SchemaFromPostIdWithDatesAsTimestamps($inner);

        $result = $sut->getSchema(1);

        $this->assertIsInt($result->getProperty('eventSchedule')[0]['startDate']);
        $this->assertIsInt($result->getProperty('eventSchedule')[0]['endDate']);
    }
}
