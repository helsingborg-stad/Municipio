<?php

namespace Municipio\PostTypeDesign\InlineCssDecorators;

class AddValuesFromExistingValues implements InlineCssDecoratorInterface {
    private array $extraCssVariables = [
        '--c-nav-v-color-contrasting' => '--color-secondary-contrasting',
        '--c-nav-v-color-contrasting-active' => '--color-secondary-contrasting',
        '--c-nav-v-item-background' => '--color-secondary',
        '--c-nav-v-background-active' => '--color-secondary-dark'
    ];

    public function __construct(private array $designConfig, private array $fields)
    {}

    public function decorate(array $inlineCss): array
    {
        foreach ($this->extraCssVariables as $variable => $value) {
            if (empty($inlineCss[$value])) {
                continue;
            }

            $inlineCss[$variable] = $inlineCss[$value];
        }

        return $inlineCss;
    }
}