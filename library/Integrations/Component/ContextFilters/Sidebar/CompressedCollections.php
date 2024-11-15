<?php

namespace Municipio\Integrations\Component\ContextFilters\Sidebar;

use Municipio\HooksRegistrar\Hookable;
use WpService\Contracts\AddFilter;

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

    public function addHooks(): void
    {
        $this->wpService->addFilter('ComponentLibrary/Component/Collection/Data', array($this, 'filterComponentData'));
    }

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
