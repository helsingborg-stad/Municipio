<?php

namespace Modularity\Options;

use WP_Post_Type;

/**
 * Class ArchivesAdminPage
 * Implements the AdminPageInterface for managing archive modules.
 */
class ArchivesAdminPage implements \Modularity\Options\AdminPageInterface
{
    /**
     * @var array The array of enabled post types for archive modules.
     */
    private array $postTypes;

    /**
     * ArchivesAdminPage constructor.
     * Initializes the class and retrieves enabled post types from options.
     */
    public function __construct()
    {
        $options = get_option('modularity-options');
        $this->postTypes = $options['enabled-post-types'] ?? [];
    }

    /**
     * Add hooks for this admin page.
     */
    public function addHooks(): void
    {
        add_action('admin_menu', [$this, 'addAdminPage'], 10);
        add_action('after_setup_theme', [$this, 'fixBrokenArchiveLinks'], 10);
    }

    /**
     * Add the admin page for managing archive modules to the WordPress admin menu.
     */
    public function addAdminPage(): void
    {
        foreach ($this->postTypes as $postType) {
            $postTypeObject = get_post_type_object($postType);

            if ($this->postTypeAllowsArchiveModules($postTypeObject)) {
                $postTypeUrlParam = $postType === 'post' ? '' : '?post_type=' . $postType;
                $transcribedPostType = \Modularity\Editor::pageForPostTypeTranscribe('archive-' . $postType);
                $editorLink = "options.php?page=modularity-editor&id={$transcribedPostType}";
                add_submenu_page(
                    'edit.php' . $postTypeUrlParam,
                    __('Archive modules', 'modularity'),
                    __('Archive modules', 'modularity'),
                    'edit_posts',
                    $editorLink
                );
            }
        }
    }

    /**
     * Determine if a post type allows archive modules.
     *
     * @param WP_Post_Type|null $postType The post type to check.
     * @return bool Returns true if the post type allows archive modules, false otherwise.
     */
    private function postTypeAllowsArchiveModules(?WP_Post_Type $postType): bool
    {
        return !is_null($postType) && $postType->has_archive;
    }

    /**
     * Fix broken archive links by redirecting to the correct URL.
     */
    public function fixBrokenArchiveLinks(): void
    {
        if (
            is_admin() &&
            isset($_GET['post_type']) &&
            isset($_GET['page']) &&
            isset($_GET['id']) &&
            substr($_GET['page'], 0, 34) == "options.php?page=modularity-editor"
        ) {
            wp_redirect(admin_url($_GET['page'] . "&id=" . $_GET['id']), 302);
            exit;
        }
    }
}
