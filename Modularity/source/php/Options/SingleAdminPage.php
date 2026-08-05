<?php

declare(strict_types=1);

namespace Modularity\Options;

use WP_Post_Type;

/**
 * Class SingleAdminPage
 *
 * Implements the AdminPageInterface and adds a submenu page for post types to add modules to single objects.
 */
class SingleAdminPage implements \Modularity\Options\AdminPageInterface
{
    private const MENU_SLUG_PREFIX = 'modularity-editor-single-';

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
        add_filter('parent_file', [$this, 'setParentFile']);
    }

    /**
     * Adds a submenu page to the WordPress admin menu for each enabled post type.
     */
    public function addAdminPage(): void
    {
        foreach ($this->postTypes as $postType) {
            $postTypeObject = get_post_type_object($postType);

            if (!$postTypeObject instanceof WP_Post_Type) {
                continue;
            }

            $postTypeUrlParam = '?post_type=' . $postType;
            $transcribedPostType = \Modularity\Editor::pageForPostTypeTranscribe('single-' . $postType);
            $menuSlug = self::MENU_SLUG_PREFIX . $postType;

            $hookName = add_submenu_page(
                'edit.php' . $postTypeUrlParam,
                __('Post type modules', 'municipio'),
                __('Post type modules', 'municipio'),
                $postTypeObject->cap->edit_posts,
                $menuSlug,
                '__return_null',
            );

            if ($hookName) {
                add_action('load-' . $hookName, function () use ($transcribedPostType): void {
                    $this->redirectToEditor($transcribedPostType);
                });
            }
        }
    }

    /**
     * Keep the shared editor under its originating post type menu.
     */
    public function setParentFile(string $parentFile): string
    {
        global $plugin_page, $submenu, $submenu_file;

        $postType = $this->getCurrentPostType();

        if (!$postType) {
            return $parentFile;
        }

        $menuSlug = self::MENU_SLUG_PREFIX . $postType;
        $plugin_page = $menuSlug;

        $selectedParent = $parentFile;
        foreach ($submenu as $menuParent => $items) {
            foreach ($items as $item) {
                if ($this->isMenuItem($item[2], $menuSlug)) {
                    $selectedParent = $menuParent;
                    $submenu_file = $item[2];
                }
            }
        }

        // WordPress recalculates the parent after this filter. Remove stale
        // copies left behind when another plugin has relocated the post type menu.
        foreach ($submenu as $menuParent => &$items) {
            if ($menuParent === $selectedParent) {
                continue;
            }

            foreach ($items as $index => $item) {
                if ($this->isMenuItem($item[2], $menuSlug)) {
                    unset($items[$index]);
                }
            }
        }
        unset($items);

        return $selectedParent;
    }

    /**
     * A local submenu slug lets WordPress match the registered page hook before
     * forwarding to the shared Modularity editor with the intended target ID.
     */
    private function redirectToEditor(string|int $editorId): never
    {
        wp_safe_redirect(
            add_query_arg(
                [
                    'page' => 'modularity-editor',
                    'id' => $editorId,
                ],
                admin_url('options.php'),
            ),
        );
        exit();
    }

    private function getCurrentPostType(): ?string
    {
        if (!isset($_GET['page'], $_GET['id']) || $_GET['page'] !== 'modularity-editor' || !is_string($_GET['id']) || !str_starts_with($_GET['id'], 'single-')) {
            return null;
        }

        $postType = substr($_GET['id'], 7);

        return in_array($postType, $this->postTypes, true) ? $postType : null;
    }

    private function isMenuItem(string $menuItem, string $menuSlug): bool
    {
        return $menuItem === $menuSlug || str_ends_with($menuItem, 'page=' . $menuSlug);
    }
}
