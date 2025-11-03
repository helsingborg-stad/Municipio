<?php

namespace Municipio\PostsList\ViewUtilities;

use Municipio\PostsList\Config\AppearanceConfig\AppearanceConfigInterface;
use Municipio\PostsList\Config\AppearanceConfig\DefaultAppearanceConfig;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class GetPostColumnClassesTest extends TestCase
{
    #[TestDox('It returns a single column for 1 column configuration')]
    public function testSingleColumn(): void
    {

        $appearanceConfig = $this->getAppearanceConfigWithColumns(1);
        $viewUtility      = new GetPostColumnClasses($appearanceConfig);

        $this->assertEquals(['o-layout-grid--col-span-12'], $viewUtility->getCallable()());
    }

    #[TestDox('It returns two columns for 2 column configuration')]
    public function testTwoColumns(): void
    {
        $appearanceConfig = $this->getAppearanceConfigWithColumns(2);
        $viewUtility      = new GetPostColumnClasses($appearanceConfig);

        $this->assertEquals(
            [
                'o-layout-grid--col-span-12',
                'o-layout-grid--col-span-6@md',
            ],
            $viewUtility->getCallable()()
        );
    }

    #[TestDox('It returns three columns for 3 column configuration')]
    public function testThreeColumns(): void
    {
        $appearanceConfig = $this->getAppearanceConfigWithColumns(3);
        $viewUtility      = new GetPostColumnClasses($appearanceConfig);

        $this->assertEquals(
            [
                'o-layout-grid--col-span-12',
                'o-layout-grid--col-span-6@md',
                'o-layout-grid--col-span-4@lg',
            ],
            $viewUtility->getCallable()()
        );
    }

    #[TestDox('It returns four columns for 4 column configuration')]
    public function testFourColumns(): void
    {
        $appearanceConfig = $this->getAppearanceConfigWithColumns(4);
        $viewUtility      = new GetPostColumnClasses($appearanceConfig);

        $this->assertEquals(
            [
                'o-layout-grid--col-span-12',
                'o-layout-grid--col-span-6@sm',
                'o-layout-grid--col-span-4@md',
                'o-layout-grid--col-span-3@lg',
            ],
            $viewUtility->getCallable()()
        );
    }

    #[TestDox('It defaults to single column for unknown column configuration')]
    public function testDefaultColumns(): void
    {
        $appearanceConfig = $this->getAppearanceConfigWithColumns(99);
        $viewUtility      = new GetPostColumnClasses($appearanceConfig);

        $this->assertEquals(['o-layout-grid--col-span-12'], $viewUtility->getCallable()());
    }

    private function getAppearanceConfigWithColumns(int $numberOfColumns): AppearanceConfigInterface
    {
        return new class ($numberOfColumns) extends DefaultAppearanceConfig {
            private int $numberOfColumns;

            public function __construct(int $numberOfColumns)
            {
                $this->numberOfColumns = $numberOfColumns;
            }

            public function getNumberOfColumns(): int
            {
                return $this->numberOfColumns;
            }
        };
    }
}
