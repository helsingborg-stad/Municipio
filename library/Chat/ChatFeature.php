<?php

namespace Municipio\Chat;

use AcfService\Contracts\AddOptionsPage;
use AcfService\Contracts\GetField;
use ComponentLibrary\Renderer\BladeService\BladeServiceFactory;
use ComponentLibrary\Renderer\Renderer;
use Municipio\Chat\Admin\RegisterChatAdminPage;
use Municipio\Chat\Api\RegisterChatEndpoint;
use Municipio\Chat\Config\ChatConfig;
use Municipio\Chat\Frontend\EnqueueChatScripts;
use Municipio\Chat\Frontend\RenderGlobalChatBubble;
use Municipio\Chat\Module\RegisterChatModule;
use Municipio\Chat\PIIRedactor\PIIRedactorFactory;
use Municipio\HooksRegistrar\HooksRegistrarInterface;
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
        private GetField&AddOptionsPage $acfService,
        private EnqueueManagerInterface $enqueue,
        private HooksRegistrarInterface $hooksRegistrar,
    ) {}

    public function enable(): void
    {
        $config = new ChatConfig($this->acfService);
        $renderer = new Renderer(
            (new BladeServiceFactory($this->wpService))->create([__DIR__ . '/views']),
        );
        $endpoint = new ChatEndpoint(
            $this->acfService,
            (new PIIRedactorFactory())->create(),
        );

        $this->hooksRegistrar->register(new RegisterChatAdminPage($this->wpService, $this->acfService));
        $this->hooksRegistrar->register(new EnqueueChatScripts($this->wpService, $this->enqueue, $config));
        $this->hooksRegistrar->register(new RenderGlobalChatBubble($this->wpService, $renderer, $config));
        $this->hooksRegistrar->register(new RegisterChatModule($this->wpService, $config));
        $this->hooksRegistrar->register(new RegisterChatEndpoint($endpoint, $config));
    }
}
