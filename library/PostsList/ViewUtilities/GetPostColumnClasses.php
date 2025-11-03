<?php

namespace Municipio\PostsList\ViewUtilities;

use Municipio\PostsList\Config\AppearanceConfig\AppearanceConfigInterface;

/*
 * View utility to get post column classes
 */
class GetPostColumnClasses implements ViewUtilityInterface
{
    /**
     * Constructor
     */
    public function __construct(private AppearanceConfigInterface $appearanceConfig)
    {
    }

    /**
     * Get the callable for the view utility
     *
     * @return callable
     */
    public function getCallable(): callable
    {
        return fn() => $this->getColumnsClassesFromNumberOfColumns(
            $this->appearanceConfig->getNumberOfColumns()
        );
    }

    private function getColumnsClassesFromNumberOfColumns(int $numberOfColumns): array
    {
        $classes = [ 'o-layout-grid--col-span-12' ];

        return match ($numberOfColumns) {
            2 => [...$classes, 'o-layout-grid--col-span-6@md'],
            3 => [...$classes, 'o-layout-grid--col-span-6@md', 'o-layout-grid--col-span-4@lg'],
            4 => [...$classes, 'o-layout-grid--col-span-6@sm', 'o-layout-grid--col-span-4@md', 'o-layout-grid--col-span-3@lg'],
            default => $classes,
        };
    }
}
