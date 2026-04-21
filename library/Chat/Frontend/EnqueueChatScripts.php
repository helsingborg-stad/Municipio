<?php

namespace Municipio\Chat\Frontend;

use Municipio\Chat\Config\ChatConfigInterface;
use Municipio\HooksRegistrar\Hookable;
use WpService\Contracts\__;
use WpService\Contracts\AddAction;
use WpUtilService\Features\Enqueue\EnqueueManagerInterface;

class EnqueueChatScripts implements Hookable
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
            ->with()
            ->translation('municipioChatStrings', [
                'sending' => $this->wpService->__('Sending...', 'municipio'),
                'writing' => $this->wpService->__('Writing...', 'municipio'),
                'usingTools' => $this->wpService->__('Using tools...', 'municipio'),
                'error' => $this->wpService->__('An error occurred. Try again later.', 'municipio'),
            ]);
    }
}
