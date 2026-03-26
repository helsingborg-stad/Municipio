<?php

namespace Municipio\Chat;

use AcfService\AcfService;
use Municipio\HooksRegistrar\Hookable;
use WpService\WpService;

class RegisterChatFieldGroupsAdminPage implements Hookable
{
    public function __construct(
        private WpService $wpService,
        private AcfService $acfService,
    ) {}

    public function addHooks(): void
    {
        $this->wpService->addAction('init', [$this, 'addAdminPage']);
    }

    public function addAdminPage(): void
    {
        $this->acfService->addOptionsPage([
            'page_title' => $this->wpService->__('Chat Settings', 'municipio'),
            'menu_title' => $this->wpService->__('Chat Settings', 'municipio'),
            'menu_slug' => 'chat-settings',
            'capability' => 'edit_posts',
            'redirect' => true,
            'update_button' => $this->wpService->__('Save', 'municipio'),
            'updated_message' => $this->wpService->__('Chat settings has been saved.', 'municipio'),
            'icon_url' => 'dashicons-format-chat',
        ]);
    }
}
