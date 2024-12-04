<?php

namespace Municipio\Integrations\Component\ContextFilters\Sidebar;

use Municipio\HooksRegistrar\Hookable;
use WpService\Contracts\AddFilter;

/**
 * Compressed collections
 */
class CompressedCollections implements Hookable
{
    /**
     * Constructor
     *
     * @param array $data The data array to resolve from
     */
    public function __construct(private AddFilter $wpService, private CurrentSidebar $currentSidebar)
    {
    }

    /**
     * Adds hooks for the CompressedCollections component.
     */
    public function addHooks(): void
    {
        $this->wpService->addFilter('ComponentLibrary/Component/Collection/Data', array($this, 'filterComponentData'));
    }

    /**
     * Filters the component data based on the current sidebar context.
     *
     * @param array $data The component data to be filtered.
     * @return array The filtered component data.
     */
    public function filterComponentData($data)
    {
        $context = $this->currentSidebar->getCurrentSidebar();

        $sidebarContexts = [
            'left-sidebar',
            'right-sidebar',
            'left-sidebar-bottom',
            'right-sidebar-bottom',
        ];

        if (in_array($context, $sidebarContexts)) {
            $data['classList']   = is_array($data['classList']) ? $data['classList'] : [];
            $data['classList'][] = 'c-collection--compact-lg';
        }

        return $data;
    }
}
