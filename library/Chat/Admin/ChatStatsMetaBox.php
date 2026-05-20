<?php

namespace Municipio\Chat\Admin;

use Municipio\Chat\Api\ChatStatsEndpoint;
use Municipio\HooksRegistrar\Hookable;
use WpService\Contracts\__;
use WpService\Contracts\AddAction;
use WpService\Contracts\AddMetaBox;
use WpService\Contracts\GetOption;

class ChatStatsMetaBox implements Hookable
{
    public function __construct(
        private __&AddAction&AddMetaBox&GetOption $wpService,
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

        echo '<table class="widefat fixed striped">';
        echo '<tbody>';
        echo '<tr><td>' . esc_html($this->wpService->__('Total messages sent', 'municipio')) . '</td><td>' . esc_html((string) $messages) . '</td></tr>';
        echo '<tr><td>' . esc_html($this->wpService->__('Total liked messages', 'municipio')) . '</td><td>' . esc_html((string) $liked) . '</td></tr>';
        echo '<tr><td>' . esc_html($this->wpService->__('Total disliked messages', 'municipio')) . '</td><td>' . esc_html((string) $disliked) . '</td></tr>';
        echo '</tbody></table>';
    }
}
