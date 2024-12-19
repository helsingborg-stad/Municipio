<?php

namespace Municipio\Integrations\MiniOrange;

use Municipio\HooksRegistrar\Hookable;
use WpService\WpService;

class MoveAdminPageToSettings implements Hookable
{
    private array $movableConfiguration;
    private string $oldParentSlug = 'mo_saml_settings';
    private string $newParentSlug = 'options-general.php';

    public function __construct(private WpService $wpService)
    {
        $this->movableConfiguration = array(
        'mo_saml_settings' => __("SSO Settings", 'municipio'),
        );
    }

    public function addHooks(): void
    {
        $this->wpService->addAction('admin_menu', array($this, 'moveAdminPage'), 99);
    }

  /**
   * Move the MiniOrange SAML settings page to the settings menu
   *
   * @return void
   */
    public function moveAdminPage()
    {
        global $menu, $submenu;

      //Declare
        $oldParentSlug = $this->oldParentSlug;
        $newParentSlug = $this->newParentSlug;

      // Move the parent menu item
        foreach ($menu as $key => $menuItem) {
            if (isset($menuItem[2]) && $menuItem[2] === $oldParentSlug) {
                unset($menu[$key]);
                break;
            }
        }

      // Move the submenu items to the new parent
        if (isset($submenu[$oldParentSlug])) {
            foreach ($submenu[$oldParentSlug] as $key => $subMenuItem) {
                if (array_key_exists($subMenuItem[2], $this->movableConfiguration)) {
                    $subMenuItem[0] = $this->movableConfiguration[$subMenuItem[2]];
                    $this->addToSubmenuIfNotExists($newParentSlug, $subMenuItem);
                }
            }

            foreach ($submenu[$oldParentSlug] as $key => $subMenuItem) {
                unset($submenu[$oldParentSlug][$key]);
            }

            $submenu[$oldParentSlug] = array_values($submenu[$oldParentSlug]);
        }
    }

  /**
   * Add a submenu item to a parent if it doesn't already exist
   *
   * @param string $newParentSlug
   * @param array $subMenuItem
   *
   * @return void
   */
    private function addToSubmenuIfNotExists($newParentSlug, $subMenuItem)
    {
        global $submenu;

        if (!isset($submenu[$newParentSlug])) {
            $submenu[$newParentSlug] = [];
        }

        foreach ($submenu[$newParentSlug] as $existingSubMenuItem) {
            if (isset($existingSubMenuItem[2]) && $existingSubMenuItem[2] === $subMenuItem[2]) {
                return;
            }
        }
        $submenu[$newParentSlug][] = $subMenuItem;
    }
}
