<?php

namespace Municipio\AcfFieldContentModifiers;

use WpService\Contracts\AddFilter;

class Registrar implements AcfFieldContentModifierRegistrarInterface
{
    public function __construct(private AddFilter $wpService)
    {
    }

    public function registerModifier(AcfFieldContentModifierInterface $modifier): void
    {
        // $this->wpService->addFilter("acf/load_field/key={$modifier->getFieldKey()}", [$modifier, 'modifyFieldContent']);
        $this->wpService->addFilter("acf/prepare_field/key={$modifier->getFieldKey()}", [$modifier, 'modifyFieldContent']);
    }
}
