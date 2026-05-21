<?php

namespace Municipio\Chat\Admin;

use Municipio\Chat\Api\ChatStatsEndpoint;
use Municipio\HooksRegistrar\Hookable;
use WpService\Contracts\__;
use WpService\Contracts\AddAction;
use WpService\Contracts\AddMetaBox;
use WpService\Contracts\GetOption;
use ComponentLibrary\Renderer\RendererInterface as BladeRenderInterface;



class ChatStatsMetaBox implements Hookable
{
    public function __construct(
        private __&AddAction&AddMetaBox&GetOption $wpService,
        private BladeRenderInterface $renderer
    ) {}

    public function addHooks(): void
    {
        //Needs to run at a specific time to ensure the meta box can be added.
        $this->wpService->addAction('admin_menu', [$this, 'register']);
    }

    public function register(): void
    {
        if (($_GET['page'] ?? '') !== 'chat-settings') {
            return;
        }

        $this->wpService->addMetaBox(
            'chat-statistics',
            $this->wpService->__('Chat Statistics', 'municipio'),
            [$this, 'render'],
            'acf_options_page',
            'side'
        );
    }

    public function render(): void
    {
        $messages = (int) $this->wpService->getOption(ChatStatsEndpoint::OPTION_MESSAGES, 0);
        $liked    = (int) $this->wpService->getOption(ChatStatsEndpoint::OPTION_LIKED, 0);
        $disliked = (int) $this->wpService->getOption(ChatStatsEndpoint::OPTION_DISLIKED, 0);

        $total = $messages;
        $neutral = $messages - ($liked + $disliked);
        $likedPercent    = $total > 0 ? ($liked / $total) * 100 : 0;
        $dislikedPercent = $total > 0 ? ($disliked / $total) * 100 : 0;
        $neutralPercent  = $total > 0 ? ($neutral / $total) * 100 : 0;
        $likedOffset = 0;
        $dislikedOffset = -$likedPercent;
        $neutralOffset = -($likedPercent + $dislikedPercent);

        $lang = [
            'totalMessages'    => $this->wpService->__('Total Messages', 'municipio'),
            'likedMessages'    => $this->wpService->__('Liked', 'municipio'),
            'dislikedMessages' => $this->wpService->__('Disliked', 'municipio'),
            'neutralMessages'  => $this->wpService->__('Neutral', 'municipio'),
        ];

        echo $this->renderer->render('stats', [
            'lang'            => $lang,
            'messages'        => $messages,
            'liked'           => $liked,
            'disliked'        => $disliked,
            'neutral'         => $neutral,
            'neutralPercent'  => $neutralPercent,
            'likedPercent'    => $likedPercent,
            'dislikedPercent' => $dislikedPercent,
            'neutralOffset'   => $neutralOffset,
            'likedOffset'     => $likedOffset,
            'dislikedOffset'  => $dislikedOffset,
        ]);
    }
}
