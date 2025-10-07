<?php

declare(strict_types=1);

namespace Municipio\Controller\School;

use Municipio\Schema\Action;
use Municipio\Schema\Schema;
use PHPUnit\Framework\TestCase;

class ActionsGeneratorTest extends TestCase
{
    /**
     * Helper to create a Preschool mock.
     */
    private function createPreschool($potentialAction)
    {
        return Schema::preschool()->potentialAction($potentialAction);
    }

    /**
     * Helper to create an Action mock.
     */
    private function createAction($description, $title, $url): Action
    {
        return Schema::action()
            ->description($description)
            ->title($title)
            ->url($url);
    }

    public function testCanBeInstantiated(): void
    {
        // Arrange
        $preschool = $this->createMock(\Municipio\Schema\Preschool::class);

        // Act
        $generator = new ActionsGenerator($preschool);

        // Assert
        $this->assertInstanceOf(ActionsGenerator::class, $generator);
    }

    public function testGenerateReturnsEmptyArrayIfPotentialActionIsNotArray(): void
    {
        // Arrange
        $preschool = $this->createPreschool(null);

        // Act
        $generator = new ActionsGenerator($preschool);

        // Assert
        $this->assertSame([], $generator->generate());
    }

    public function testGenerateReturnsEmptyArrayIfPotentialActionIsEmptyArray(): void
    {
        // Arrange
        $preschool = $this->createPreschool([]);

        // Act
        $generator = new ActionsGenerator($preschool);

        // Assert
        $this->assertEquals([], $generator->generate());
    }

    public function testGenerateReturnsCorrectDataForValidActions(): void
    {
        // Arrange
        $action    = $this->createAction('desc', 'Action Title', 'https://example.com');
        $preschool = $this->createPreschool([$action]);

        // Act
        $generator = new ActionsGenerator($preschool);
        $result    = $generator->generate();

        // Assert
        $this->assertEquals('desc', $result['description']);
        $this->assertEquals([
            [
                'text'  => 'Action Title',
                'href'  => 'https://example.com',
                'color' => 'primary',
            ]
        ], $result['buttonsArgs']);
    }
}
