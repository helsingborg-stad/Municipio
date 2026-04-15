<?php

namespace Municipio\Styleguide\Customize\TokenData\Decorators;

use Modularity\HooksRegistrar\Hookable;
use Municipio\Styleguide\Customize\OverrideState\OverrideStateInterface;
use WpService\Contracts\AddFilter;

/**
 * Ensure that the font chosen and stored for OverrideState is added to the options list, otherwise it will be lost when the user opens the select again and saves without changing the value.
 */
class AddOverrideFontFamilies implements Hookable
{
    public function __construct(
        private AddFilter $wpService,
        private OverrideStateInterface $overrideState,
    ) {}

    public function addHooks(): void
    {
        $this->wpService->addFilter('Municipio/Styleguide/Customize/TokenData/FontFamilies', [$this, 'addOverrideFontFamilies']);
    }

    public function addOverrideFontFamilies(array $options): array
    {
        $state = $this->overrideState->getOverrideState();

        foreach (['--font-family-base', '--font-family-heading'] as $variable) {
            if (!empty($state['token'][$variable]) && is_string($state['token'][$variable])) {
                $options[] = [
                    'value' => $state['token'][$variable],
                    'label' => $state['token'][$variable],
                ];
            }
        }

        return $options;
    }
}
