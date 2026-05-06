<?php

declare(strict_types=1);

namespace Municipio\Chat\Module;

class ChatModule extends \Modularity\Module
{
    public $slug = 'chat';
    public $supports = [];

    public function init()
    {
        $this->nameSingular = __('Chat', 'municipio');
        $this->namePlural = __('Chats', 'municipio');
        $this->description = __('Outputs a chat interface', 'municipio');
    }

    public function data(): array
    {
        $data = $this->getFields() ?? [];
        $data['id'] = uniqid('chat-');

        $data['lang'] = [
            'chat' => __('Chat', 'municipio'),
            'send' => __('Send', 'municipio'),
            'placeholder' => __('Write your question here', 'municipio'),
        ];

        return $data;
    }

    public function template()
    {
        return 'chat.blade.php';
    }
}
