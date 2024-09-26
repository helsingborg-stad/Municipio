<?php

namespace Municipio\Controller\Navigation;

use Municipio\Controller\Navigation\Config\MenuConfigInterface;
use Municipio\Controller\Navigation\Menu;
use Municipio\Controller\Navigation\Decorators\RemoveTopLevelDecorator;
use Municipio\Controller\Navigation\Decorators\RemoveSubLevelDecorator;
use Municipio\Controller\Navigation\Decorators\StructureMenuItemsDecorator;

// Static
use Municipio\Controller\Navigation\ComplementDefaultDecoratorFactory;
use Municipio\Controller\Navigation\ComplementPageTreeFallbackDecoratorFactory;
use Municipio\Controller\Navigation\Config\MenuConfig;

class MenuFactory {
    private array $decorators;

    public function __construct(
        private MenuConfigInterface $menuConfig, 
    ) {
        $this->decorators = $this->getDefaultDecorators();
    }

    private function getDefaultDecorators(): array {
        return [
            new RemoveTopLevelDecorator(),
            new RemoveSubLevelDecorator(),
        ];
    }

    // public static function createMenuStatic(): Menu
    // {
    //     return new Menu(
    //         new MenuConfig(
    //             ComplementDefaultDecoratorFactory::createComplementDecoratorStatic(),
    //             ComplementPageTreeFallbackDecoratorFactory::createComplementDecoratorStatic()
    //         ),
    //         self::getDefaultDecorators()
    //     );
    // }

    public function createMenu(): Menu {
        return new Menu(
            $this->menuConfig,
            $this->decorators
        );
    }

    public function getDecorators(): array {
        return $this->decorators;
    }

    public function setDecorators(array $decorators): void
    {
        $this->decorators = $decorators;
    }
}