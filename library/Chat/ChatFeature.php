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

        (new ChatAdminPage($this->wpService, $this->acfService))->addHooks();

        if (!$config->isEnabled()) {
            return;
        }

        $bladeRenderer = new BladeRenderer((new BladeServiceFactory($this->wpService))->create(ChatRender::getViewPathsDir()));

          $render = new ChatRender(
            $bladeRenderer,
        );

        RestApiEndpointsRegistry::add(new ChatEndpoint($config, (new PIIRedactorFactory())->create()));
        RestApiEndpointsRegistry::add(new ChatStatsEndpoint($this->wpService));

        (new ChatBlock($this->wpService, $config, $render))->addHooks();
        (new ChatStatsMetaBox($this->wpService, $bladeRenderer))->addHooks();
        (new ChatEnqueue($this->wpService, $this->enqueue, $config))->addHooks();
        (new ChatBubble($this->wpService, $config, $render))->addHooks();
    }
}
