<?php

namespace Municipio\GlobalNotices;

use WpService\WpService;
use AcfService\AcfService;
use Municipio\HooksRegistrar\Hookable;

class RegisterGlobalNoticesFieldGroupsAdminPage implements Hookable
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
        $this->acfService->addOptionsPage([
            'page_title'      => $this->wpService->__('Global Notices', 'municipio'),
            'menu_title'      => $this->wpService->__('Global Notices', 'municipio'),
            'menu_slug'       => 'global-notices',
            'capability'      => 'edit_posts',
            'redirect'        => true,
            'update_button'   => $this->wpService->__('Save', 'municipio'),
            'updated_message' => $this->wpService->__('Global notices has been saved.', 'municipio'),
            'icon_url'        => 'dashicons-megaphone'
        ]);
    }
}
