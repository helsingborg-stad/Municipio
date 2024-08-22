<?php

namespace Municipio\AcfFieldContentModifiers;

use WpService\Contracts\AddAction;

class Registrar implements AcfFieldContentModifierRegistrarInterface
{
    public function __construct(private AddAction $wpService)
    {
    }

    public function registerModifier(AcfFieldContentModifierInterface $modifier): void
    {
        $this->wpService->addAction("acf/load_field/key={$modifier->getFieldKey()}", [$modifier, 'modifyFieldContent']);
    }
}
