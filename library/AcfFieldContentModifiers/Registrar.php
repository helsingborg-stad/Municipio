<?php

namespace Municipio\AcfFieldContentModifiers;

use WpService\Contracts\AddFilter;
use WpService\Contracts\GetPostType;

class Registrar implements AcfFieldContentModifierRegistrarInterface
{
    private AcfFieldContentModifierInterface $modifier;

    public function __construct(private AddFilter&GetPostType $wpService)
    {
    }

    public function registerModifier(AcfFieldContentModifierInterface $modifier): void
    {
        $this->modifier = $modifier;
        $this->wpService->addFilter("acf/prepare_field/key={$this->modifier->getFieldKey()}", [$this, 'applyModifier']);
    }

    public function applyModifier(array $field): array
    {
        if ($this->wpService->getPostType() === 'acf-field-group') {
            // Don't run when on the field group edit screen.
            return $field;
        }

        return $this->modifier->modifyFieldContent($field);
    }
}
