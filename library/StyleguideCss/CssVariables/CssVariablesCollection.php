<?php

namespace Municipio\StyleguideCss\CssVariables;

class CssVariablesCollection implements CssVariablesCollectionInterface
{
    private array $cssVariables = [];

    public function __construct(
        array $cssVariables,
    ) {
        $this->addCssVariables(...$cssVariables);
    }

    /**
     * Add one or more CSS variables to the collection
     * Primary purpose is to validate that all items are instances of CssVariableInterface.
     *
     * @param CssVariableInterface ...$cssVariables One or more CSS variable objects to add to the collection
     */
    private function addCssVariables(CssVariableInterface ...$cssVariables): void
    {
        foreach ($cssVariables as $cssVariable) {
            $this->cssVariables[] = $cssVariable;
        }
    }

    public function getVariables(): array
    {
        return $this->cssVariables;
    }

    public function __toString(): string
    {
        return (
            implode(PHP_EOL, array_map(
                static fn($cssVariable) => sprintf('%s: %s;', $cssVariable->getName(), $cssVariable->getValue()),
                $this->getVariables(),
            )) . PHP_EOL
        );
    }
}
