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

    public function registerModifier(string $fieldKey, AcfFieldContentModifierInterface $modifier): void
    {
        $this->wpService->addFilter(
            "acf/prepare_field/key={$fieldKey}",
            function (array $field) use ($modifier) {
                return $this->applyModifier($modifier, $field);
            }
        );
    }

    public function applyModifier(AcfFieldContentModifierInterface $modifier, array $field): array
    {
        if ($this->wpService->getPostType() === 'acf-field-group') {
            // Don't run when on the field group edit screen.
            return $field;
        }

        return $modifier->modifyFieldContent($field);
    }
}
