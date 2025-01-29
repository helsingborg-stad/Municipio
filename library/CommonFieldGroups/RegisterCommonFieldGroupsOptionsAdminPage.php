<?php

namespace Municipio\CommonFieldGroups;

use WpService\WpService;
use AcfService\AcfService;
use Municipio\HooksRegistrar\Hookable;

class RegisterCommonFieldGroupsOptionsAdminPage implements Hookable
{
    public function __construct(private WpService $wpService, private AcfService $acfService)
    {
    }

    public function addHooks(): void
    {
        $this->wpService->addAction('init', [$this, 'addAdminPage']);
    }

    public function addAdminPage(): void
    {
        if (!$this->wpService->isMainSite()) {
            return;
        }

        $this->acfService->addOptionsPage([
            'page_title'      => $this->wpService->__('Common Field Groups', 'municipio'),
            'menu_title'      => $this->wpService->__('Common Field Groups', 'municipio'),
            'menu_slug'       => 'common-field-groups',
            'capability'      => 'manage_options',
            'redirect'        => true,
            'update_button'   => $this->wpService->__('Save', 'municipio'),
            'updated_message' => $this->wpService->__('Common field groups settings has been saved.', 'municipio'),
            'parent_slug'     => 'options-general.php',
        ]);
    }
}
