<?php

declare(strict_types=1);

namespace Municipio\StyleguideCss\AddCustomizePropertiesToComponents;

use Municipio\HooksRegistrar\Hookable;
use WpService\WpService;

/**
 * Class AddCustomizerPropertiesToComponents
 *
 * Adds customizer properties to styleguide components.
 *
 * @package Municipio\StyleguideCss\AddCustomizePropertiesToComponents
 */
class AddCustomizerPropertiesToComponents implements Hookable
{
    private const COMPONENT_FILTER = 'ComponentLibrary/Component/Data';

    public function __construct(
        private readonly WpService $wpService,
    ) {}

    /* Registers the necessary hooks for adding customizer properties to components.
     *
     * This method checks if the editor should be enabled based on the current user's capabilities.
     * If the editor is enabled, it adds a filter to modify the component data with customizer properties.
     */
    public function addHooks(): void
    {
        if (!$this->shouldEnableEditor()) {
            return;
        }

        $this->wpService->addFilter(self::COMPONENT_FILTER, [$this, 'addCustomizerProperties']);
    }

    /* Checks if the editor should be enabled based on the current user's capabilities.
     *
     * @return bool True if the editor should be enabled, false otherwise.
     */
    private function shouldEnableEditor(): bool
    {
        return $this->wpService->isAdmin() && $this->wpService->currentUserCan('edit_theme_options');
    }

    /** Adds customizer properties to the component data.
     *
     * @param array $componentData The original component data.
     * @return array The modified component data with customizer properties.
     */
    public function addCustomizerProperties(array $componentData): array
    {
        $componentData['attributeList']['data-customizer'] = json_encode([
            'type' => 'string',
            'default' => 'test',
        ]);

        return $componentData;
    }
}
