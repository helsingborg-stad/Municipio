<?php

namespace Municipio\Chat\Config;

interface ChatConfigInterface
{
    public function isEnabled(): bool;

    public function isGlobalChatEnabled(): bool;
}
