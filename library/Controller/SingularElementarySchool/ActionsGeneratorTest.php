<?php

declare(strict_types=1);

namespace Municipio\Controller\SingularElementarySchool;

use Municipio\Schema\Action;
use Municipio\Schema\Schema;
use PHPUnit\Framework\TestCase;

class ActionsGeneratorTest extends TestCase
{
    /**
     * Helper to create an ElementarySchool mock.
     */
    private function createElementarySchool($potentialAction)
    {
        return Schema::elementarySchool()->potentialAction($potentialAction);
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

    /**
     * @test
     * It can be instantiated.
     */
    public function testCanBeInstantiated(): void
    {
        // Arrange
        $elementarySchool = $this->createMock(\Municipio\Schema\ElementarySchool::class);

        // Act
        $generator = new ActionsGenerator($elementarySchool);

        // Assert
        $this->assertInstanceOf(ActionsGenerator::class, $generator);
    }

    /**
     * @test
     * It returns empty array if potentialAction is not an array.
     */
    public function testGenerateReturnsEmptyArrayIfPotentialActionIsNotArray(): void
    {
        // Arrange
        $elementarySchool = $this->createElementarySchool(null);

        // Act
        $generator = new ActionsGenerator($elementarySchool);

        // Assert
        $this->assertSame([], $generator->generate());
    }

    /**
     * @test
     * It returns empty array if potentialAction is an empty array.
     */
    public function testGenerateReturnsEmptyArrayIfPotentialActionIsEmptyArray(): void
    {
        // Arrange
        $elementarySchool = $this->createElementarySchool([]);

        // Act
        $generator = new ActionsGenerator($elementarySchool);

        // Assert
        $this->assertEquals([], $generator->generate());
    }

    /**
     * @test
     * It returns correct data for valid actions.
     */
    public function testGenerateReturnsCorrectDataForValidActions(): void
    {
        // Arrange
        $action           = $this->createAction('desc', 'Action Title', 'https://example.com');
        $elementarySchool = $this->createElementarySchool([$action]);

        // Act
        $generator = new ActionsGenerator($elementarySchool);
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
