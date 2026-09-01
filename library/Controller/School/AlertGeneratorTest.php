<?php

declare(strict_types=1);

namespace Municipio\Controller\School;

use Municipio\Schema\Schema;
use PHPUnit\Framework\TestCase;

class AlertGeneratorTest extends TestCase
{
    public function testCanBeInstantiatedWithElementarySchool(): void
    {
        // Arrange
        $school = Schema::elementarySchool();

        // Act
        $generator = new AlertGenerator($school);

        // Assert
        static::assertInstanceOf(AlertGenerator::class, $generator);
    }

    public function testGenerateReturnsAlertFromDescription(): void
    {
        // Arrange
        $school = Schema::elementarySchool()->description([
            Schema::textObject()->name('role:preamble')->headline('Preamble')->text('Preamble text'),
            Schema::textObject()->name('role:alert')->headline('School alert')->text('School alert text'),
        ]);
        $generator = new AlertGenerator($school);

        // Act
        $result = $generator->generate();

        // Assert
        static::assertSame([
            'title' => 'School alert',
            'text'  => 'School alert text',
        ], $result);
    }

    public function testGenerateReturnsFirstMatchingAlert(): void
    {
        // Arrange
        $school = Schema::preschool()->description([
            Schema::textObject()->name('role:alert')->headline('First alert')->text('First alert text'),
            Schema::textObject()->name('role:alert')->headline('Second alert')->text('Second alert text'),
        ]);
        $generator = new AlertGenerator($school);

        // Act
        $result = $generator->generate();

        // Assert
        static::assertSame([
            'title' => 'First alert',
            'text'  => 'First alert text',
        ], $result);
    }

    public function testGenerateReturnsNullWhenDescriptionHasNoMatchingAlert(): void
    {
        // Arrange
        $school = Schema::elementarySchool()->description([
            Schema::textObject()->name('role:preamble')->headline('Preamble')->text('Preamble text'),
        ]);
        $generator = new AlertGenerator($school);

        // Act
        $result = $generator->generate();

        // Assert
        static::assertNull($result);
    }

    public function testGenerateReturnsNullWhenAlertHeadlineIsMissing(): void
    {
        // Arrange
        $school = Schema::elementarySchool()->description([
            Schema::textObject()->name('role:alert')->text('School alert text'),
        ]);
        $generator = new AlertGenerator($school);

        // Act
        $result = $generator->generate();

        // Assert
        static::assertNull($result);
    }

    public function testGenerateReturnsNullWhenAlertTextIsMissing(): void
    {
        // Arrange
        $school = Schema::elementarySchool()->description([
            Schema::textObject()->name('role:alert')->headline('School alert'),
        ]);
        $generator = new AlertGenerator($school);

        // Act
        $result = $generator->generate();

        // Assert
        static::assertNull($result);
    }

    public function testGenerateReturnsNullForNonArrayDescription(): void
    {
        // Arrange
        $school = Schema::elementarySchool()->description('School description');
        $generator = new AlertGenerator($school);

        // Act
        $result = $generator->generate();

        // Assert
        static::assertNull($result);
    }
}