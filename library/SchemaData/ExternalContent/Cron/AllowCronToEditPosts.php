<?php

namespace Municipio\SchemaData\ExternalContent\Cron;

use Municipio\HooksRegistrar\Hookable;
use WpService\Contracts\AddAction;
use WpService\Contracts\AddFilter;
use WpService\Contracts\WpDoingCron;

/**
 * Class AllowCronToEditPosts
 *
 * Due to the capability 'edit_posts' being required run wp_set_post_terms()
 * during wp_insert_post(), this class temporarily enabled this capability for
 * cron jobs while they are running.
 */
class AllowCronToEditPosts implements Hookable
{
    /**
     * Class constructor
     */
    public function __construct(private AddAction&AddFilter&WpDoingCron $wpService)
    {
    }

    /**
     * @inheritDoc
     */
    public function addHooks(): void
    {
        if (!$this->wpService->wpDoingCron()) {
            return;
        }

        $this->wpService->addAction('Municipio/ExternalContent/Sync', [$this, 'addCapabilitiesFilter'], 1);
        $this->wpService->addAction('Municipio/ExternalContent/Sync', [$this, 'removeCapabilitiesFilter'], 100);
    }

    /**
     * Add capabilities filter.
     */
    public function addCapabilitiesFilter(): void
    {
        $this->wpService->addFilter('user_has_cap', [$this, 'allowEditPosts'], 10, 3);
    }

    /**
     * Remove capabilities filter.
     */
    public function removeCapabilitiesFilter(): void
    {
        // TODO: Replace with removeFilter() when it's available in WpService.
        remove_filter('user_has_cap', [$this, 'allowEditPosts'], 10);
    }

    /**
     * Allow edit_posts capability.
     * @param bool[] $allcaps
     * @param string[] $caps
     * @param array $args
     *
     * @return bool[]
     */
    public function allowEditPosts(array $allcaps, array $caps, array $args): array
    {
        if ('edit_posts' === $args[0]) {
            $allcaps['edit_posts'] = true;
        }

        return $allcaps;
    }
}
