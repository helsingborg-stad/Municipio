<?php

namespace Municipio\Chat\Admin;

use AcfService\Contracts\AddOptionsPage;
use Municipio\HooksRegistrar\Hookable;
use WpService\Contracts\__;
use WpService\Contracts\AddAction;
use WpService\Contracts\AddFilter;
use WpService\Contracts\GetPostTypeObject;
use WpService\Contracts\IsPostTypeHierarchical;
use WpService\Contracts\IsPostTypeViewable;

class ChatAdminPage implements Hookable
{
    public function __construct(
        private __&AddAction&AddFilter&GetPostTypeObject&IsPostTypeHierarchical&IsPostTypeViewable $wpService,
        private AddOptionsPage $acfService,
    ) {}

    public function addHooks(): void
    {
        $this->wpService->addAction('init', [$this, 'register']);
        $this->wpService->addFilter('acf/fields/post_object/query/name=chat_assistant_pages', [$this, 'filterAssistantPagesField'], 10, 1);
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

    public function filterAssistantPagesField(array $args): array
    {
        $wpService = $this->wpService;
        $args['post_type'] = array_filter($args['post_type'] ?? [], static function ($postType) use ($wpService) {
            $postObj = $wpService->getPostTypeObject($postType);
            return $postObj && $wpService->isPostTypeHierarchical($postType) && $wpService->isPostTypeViewable($postObj);
        });

        return $args;
    }
}
