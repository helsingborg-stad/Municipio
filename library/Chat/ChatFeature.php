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
use Municipio\Chat\Render\ChatBubble;
use Municipio\Chat\Render\ChatEnqueue;
use Municipio\Chat\Render\ChatRender;
use WpService\Contracts\__;
use WpService\Contracts\AddAction;
use WpService\Contracts\AddMetaBox;
use WpService\Contracts\DetermineLocale;
use WpService\Contracts\GetBlockWrapperAttributes;
use WpService\Contracts\GetOption;
use WpService\Contracts\IsWpError;
use WpService\Contracts\RegisterBlockType;
use WpService\Contracts\WpRemotePost;
use WpService\Contracts\WpRemoteRetrieveBody;
use WpService\Contracts\WpRemoteRetrieveResponseCode;
use WpUtilService\Features\Enqueue\EnqueueManagerInterface;

class ChatFeature
{
    public function __construct(
        private __&DetermineLocale&IsWpError&WpRemotePost&WpRemoteRetrieveBody&WpRemoteRetrieveResponseCode&RegisterBlockType&AddAction&GetBlockWrapperAttributes&AddMetaBox&GetOption $wpService,
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

        $bladeRenderer = new BladeRenderer((new BladeServiceFactory($this->wpService))->create(ChatRender::getViewPathsDir()));
        $render = new ChatRender($bladeRenderer);

        RestApiEndpointsRegistry::add(new ChatEndpoint($config, (new PIIRedactorFactory($this->wpService))->create($config)));
        RestApiEndpointsRegistry::add(new ChatStatsEndpoint($this->wpService));

        (new ChatBlock($this->wpService, $config, $render))->addHooks();
        (new ChatStatsMetaBox($this->wpService, $bladeRenderer))->addHooks();
        (new ChatEnqueue($this->wpService, $this->enqueue, $config))->addHooks();
        (new ChatBubble($this->wpService, $config, $render))->addHooks();
    }
}
