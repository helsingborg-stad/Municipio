<?php

namespace Municipio\Chat\Admin;

use AcfService\Contracts\AddOptionsPage;
use Municipio\HooksRegistrar\Hookable;
use WpService\Contracts\__;
use WpService\Contracts\AddAction;

class ChatAdminPage implements Hookable
{
    public function __construct(
        private __&AddAction $wpService,
        private AddOptionsPage $acfService,
    ) {}

    public function addHooks(): void
    {
        $this->wpService->addAction('init', [$this, 'register']);
    }

    public function register(): void
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
