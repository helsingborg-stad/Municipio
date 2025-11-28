<?php

namespace Municipio\Integrations\Component\ContextFilters\Sidebar;

use Municipio\HooksRegistrar\Hookable;
use WpService\Contracts\AddAction;

/**
 * Current sidebar
 */
class CurrentSidebar implements Hookable
{
    private $currentSidebar = '';

    /**
     * Class CurrentSidebar
     *
     * This class represents a component for handling the current sidebar in the sidebar context filters.
     */
    public function __construct(private AddAction $wpService)
    {
    }

    /**
     * Adds hooks for the CurrentSidebar component.
     *
     * This method adds a hook to the 'dynamic_sidebar_before' action, which will call the 'setCurrentSidebar' method of the CurrentSidebar component.
     * The hook has a priority of 1 and accepts 1 argument.
     */
    public function addHooks(): void
    {
        $this->wpService->addAction('dynamic_sidebar_before', array($this, 'setCurrentSidebar'), 1, 1);
    }

    /**
     * Gets the current sidebar.
     *
     * This method returns the current sidebar.
     *
     * @return string The current sidebar.
     */
    public function getCurrentSidebar()
    {
        return $this->currentSidebar;
    }

    /**
     * Sets the current sidebar.
     *
     * This method sets the current sidebar.
     *
     * @param string $sidebarName The name of the sidebar to set as the current sidebar.
     */
    public function setCurrentSidebar($sidebarName)
    {
        $this->currentSidebar = $sidebarName;
    }
}
