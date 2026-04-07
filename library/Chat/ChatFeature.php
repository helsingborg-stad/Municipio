<?php

namespace Municipio\Chat;

use AcfService\Contracts\AddOptionsPage;
use AcfService\Contracts\GetField;
use ComponentLibrary\Renderer\BladeService\BladeServiceFactory;
use ComponentLibrary\Renderer\Renderer;
use Municipio\Api\RestApiEndpointsRegistry;
use Municipio\Chat\PIIRedactor\PIIRedactorFactory;
use WpService\Contracts\__;
use WpService\Contracts\AddAction;
use WpService\Contracts\AddFilter;
use WpService\Contracts\ApplyFilters;
use WpService\Contracts\WpCacheGet;
use WpService\Contracts\WpCacheSet;
use WpUtilService\Features\Enqueue\EnqueueManagerInterface;

class ChatFeature
{
    public function __construct(
        private __&AddAction&AddFilter&ApplyFilters&WpCacheGet&WpCacheSet $wpService,
        private EnqueueManagerInterface $enqueue,
        private GetField&AddOptionsPage $acfService,
    ) {}

    public function enable(): void
    {
        $this->registerApiEndpoint();

        $this->wpService->addAction('init', [$this, 'addAdminPage']);
        $this->wpService->addAction('wp_enqueue_scripts', [$this, 'enqueueScripts']);
        $this->wpService->addAction('wp_footer', [$this, 'renderChat']);
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

    public function enqueueScripts(): void
    {
        $this->enqueue->add('js/chat.js');
    }

    public function renderChat(): void
    {
        $renderer = new Renderer((new BladeServiceFactory($this->wpService))->create([__DIR__ . '/views']));
        $markup = $renderer->render('Chat');
        echo $markup;
    }

    private function registerApiEndpoint(): void
    {
        $redactor = (new PIIRedactorFactory())->create();

        RestApiEndpointsRegistry::add(new \Municipio\Chat\ChatEndpoint($this->acfService, $redactor));
    }
}
