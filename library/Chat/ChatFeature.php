<?php

namespace Municipio\Chat;

use AcfService\Contracts\AddOptionsPage;
use AcfService\Contracts\GetField;
use ComponentLibrary\Renderer\BladeService\BladeServiceFactory;
use ComponentLibrary\Renderer\Renderer as BladeRenderer;
use Municipio\Api\RestApiEndpointsRegistry;
use Municipio\Chat\Admin\ChatAdminPage;
use Municipio\Chat\Admin\ChatStatsMetaBox;
use Municipio\Chat\Api\ChatEndpoint;
use Municipio\Chat\Api\ChatStatsEndpoint;
use Municipio\Chat\Api\RegisterChatEndpoint;
use Municipio\Chat\Api\RegisterChatStatsEndpoint;
use Municipio\Chat\Config\ChatConfig;
use Municipio\Chat\PIIRedactor\PIIRedactorFactory;
use WpUtilService\Features\Enqueue\EnqueueManagerInterface;
use Municipio\Chat\Render\ChatBubble;
use Municipio\Chat\Render\ChatEnqueue;
use Municipio\Chat\Render\ChatRender;
use WpService\WpService;

class ChatFeature
{
    public function __construct(
        private WpService $wpService,
        private GetField&AddOptionsPage $acfService,
        private EnqueueManagerInterface $enqueue,
    ) {}

    public function enable(): void
    {
        $config = new ChatConfig($this->acfService);

        if (!$config->isEnabled()) {
            return;
        }
            
        $bladeRenderer = new BladeRenderer((new BladeServiceFactory($this->wpService))->create(ChatRender::getViewPathsDir()));

          $render = new ChatRender(
            $bladeRenderer,
        );
        
        (new RegisterChatEndpoint(
            new ChatEndpoint($this->acfService, (new PIIRedactorFactory())->create()),
            $config,
        ))->addHooks();

        (new RegisterChatStatsEndpoint(
            new ChatStatsEndpoint($this->wpService),
            $config,
        ))->addHooks();

        (new ChatBlock($this->wpService, $config, $render))->addHooks();
        (new ChatAdminPage($this->wpService, $this->acfService))->addHooks();
        (new ChatStatsMetaBox($this->wpService, $bladeRenderer))->addHooks();
        (new ChatEnqueue($this->wpService, $this->enqueue, $config))->addHooks();
        (new ChatBubble($this->wpService, $config, $render))->addHooks();
    }
}
