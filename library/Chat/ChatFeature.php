<?php

declare(strict_types=1);

namespace Municipio\Chat;

use AcfService\Contracts\AddOptionsPage;
use AcfService\Contracts\GetField;
use Municipio\Helper\ComponentBladeService;
use ComponentLibrary\Renderer\Renderer as BladeRenderer;
use Municipio\Api\RestApiEndpointsRegistry;
use Municipio\Chat\Admin\ChatAdminPage;
use Municipio\Chat\Admin\ChatStatsMetaBox;
use Municipio\Chat\Config\ChatConfig;
use Municipio\Chat\Integrations\Ai\Api\ChatEndpoint;
use Municipio\Chat\Integrations\Ai\Api\ChatStatsEndpoint;
use Municipio\Chat\PIIRedactor\PIIRedactorFactory;
use Municipio\Chat\Render\ChatBubble;
use Municipio\Chat\Render\ChatEnqueue;
use Municipio\Chat\Render\ChatRender;
use WpService\WpService;
use WpUtilService\Features\Enqueue\EnqueueManagerInterface;

class ChatFeature
{
    public function __construct(
        private WpService $wpService,
        private GetField&AddOptionsPage $acfService,
        private EnqueueManagerInterface $enqueue,
    ) {}

    public function enable(): void
    {
        $config = new ChatConfig($this->wpService, $this->acfService);

        (new ChatAdminPage($this->wpService, $this->acfService))->addHooks();

        if (!$config->isEnabled()) {
            return;
        }

        $bladeRenderer = new BladeRenderer(ComponentBladeService::create(ChatRender::getViewPathsDir(), $this->wpService));
        $render = new ChatRender($bladeRenderer);

        RestApiEndpointsRegistry::add(new ChatEndpoint($config, new PIIRedactorFactory($this->wpService), $this->wpService));
        RestApiEndpointsRegistry::add(new ChatStatsEndpoint($this->wpService));

        // Acf repeater crashes when below are reigstered
        if (!$this->wpService->isAdmin() || !isset($_GET['page']) || $_GET['page'] !== 'chat-settings') {
            (new ChatBubble($this->wpService, $config, $render))->addHooks();
            (new ChatBlock($this->wpService, $config, $render))->addHooks();
            (new ChatEnqueue($this->wpService, $this->enqueue, $config))->addHooks();
        }

        (new ChatStatsMetaBox($this->wpService, $bladeRenderer))->addHooks();
    }
}
