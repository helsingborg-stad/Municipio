<?php

namespace Municipio\AcfFieldContentModifiers;

use WpService\Contracts\AddFilter;
use WpService\Contracts\GetPostType;

/**
 * Class Registrar
 *
 * @package Municipio\AcfFieldContentModifiers
 */
class Registrar implements AcfFieldContentModifierRegistrarInterface
{
    /**
     * Registrar constructor.
     *
     * @param AddFilter&GetPostType $wpService
     */
    public function __construct(private AddFilter&GetPostType $wpService)
    {
    }

    /**
     * @inheritDoc
     */
    public function registerModifier(string $fieldKey, AcfFieldContentModifierInterface $modifier): void
    {
        $this->wpService->addFilter(
            "acf/prepare_field/key={$fieldKey}",
            function (array $field) use ($modifier) {
                return $this->applyModifier($modifier, $field);
            }
        );
    }

    /**
     * Apply a modifier to a field.
     *
     * @param AcfFieldContentModifierInterface $modifier
     * @param array $field
     */
    public function applyModifier(AcfFieldContentModifierInterface $modifier, array $field): array
    {
        if ($this->wpService->getPostType() === 'acf-field-group') {
            // Don't run when on the field group edit screen.
            return $field;
        }

        return $modifier->modifyFieldContent($field);
    }
}
