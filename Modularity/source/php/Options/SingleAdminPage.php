<?php

namespace Modularity\Options;

/**
 * Class SingleAdminPage
 *
 * Implements the AdminPageInterface and adds a submenu page for post types to add modules to single objects.
 */
class SingleAdminPage implements \Modularity\Options\AdminPageInterface
{
    private array $postTypes;

    /**
     * SingleAdminPage constructor.
     *
     * Initializes the post types array with the enabled post types from the modularity-options option.
     */
    public function __construct()
    {
        $options = get_option('modularity-options');
        $this->postTypes = $options['enabled-post-types'] ?? [];
    }

    /**
     * Adds the addAdminPage method to the admin_menu action hook.
     */
    public function addHooks(): void
    {
        add_action('admin_menu', [$this, 'addAdminPage'], 10);
    }

    /**
     * Adds a submenu page to the WordPress admin menu for each enabled post type.
     */
    public function addAdminPage(): void
    {
        foreach ($this->postTypes as $postType) {
            $postTypeUrlParam = '?post_type=' . $postType;
            $transcribedPostType = \Modularity\Editor::pageForPostTypeTranscribe('single-' . $postType);
            $editorLink = "options.php?page=modularity-editor&id={$transcribedPostType}";

            add_submenu_page(
                'edit.php' . $postTypeUrlParam,
                __('Post type modules', 'modularity'),
                __('Post type modules', 'modularity'),
                'edit_posts',
                $editorLink
            );
        }
    }
}
