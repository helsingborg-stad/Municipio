<?php

namespace Municipio\Chat\Render;

use Municipio\Chat\Config\ChatConfigInterface;
use Municipio\HooksRegistrar\Hookable;
use WpService\Contracts\__;
use WpService\Contracts\AddAction;
use WpUtilService\Features\Enqueue\EnqueueManagerInterface;

class ChatEnqueue implements Hookable
{
    public function __construct(
        private __&AddAction $wpService,
        private EnqueueManagerInterface $enqueue,
        private ChatConfigInterface $config,
    ) {}

    public function addHooks(): void
    {
        if (!$this->config->isEnabled()) {
            return;
        }

        $this->wpService->addAction('wp_enqueue_scripts', [$this, 'enqueue']);
    }

    public function enqueue(): void
    {
        $this->enqueue
            ->add('js/chat.js')
            ->with();

        $this->enqueue->add('css/chat.css');
    }
}
