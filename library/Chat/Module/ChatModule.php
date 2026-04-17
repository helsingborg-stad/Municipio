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
        return $this->getFields() ?? [];
    }

    public function template()
    {
        return 'chat.blade.php';
    }
}
