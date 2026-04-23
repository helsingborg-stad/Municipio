<?php

namespace Municipio\Chat\Config;

use AcfService\Contracts\GetField;

class ChatConfig implements ChatConfigInterface
{
    public function __construct(
        private GetField $acfService,
    ) {}

    public function isEnabled(): bool
    {
        return (bool) $this->acfService->getField('chat_enabled', 'option');
    }

    public function isGlobalChatEnabled(): bool
    {
        return (bool) $this->acfService->getField('chat_global_enabled', 'option');
    }
}
