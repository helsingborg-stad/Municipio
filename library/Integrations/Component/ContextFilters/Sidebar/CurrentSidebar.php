<?php

namespace Municipio\Integrations\Component\ContextFilters\Sidebar;

use Municipio\HooksRegistrar\Hookable;
use WpService\Contracts\AddAction;

class CurrentSidebar implements Hookable
{
    private $currentSidebar = '';

    public function __construct(private AddAction $wpService)
    {}

    public function addHooks(): void
    {
        $this->wpService->addAction('dynamic_sidebar_before', array($this, 'setCurrentSidebar'), 1, 1);
    }

    public function getCurrentSidebar()
    {
        return $this->currentSidebar;
    }

    public function setCurrentSidebar($sidebarName)
    {
        $this->currentSidebar = $sidebarName;
    }
}