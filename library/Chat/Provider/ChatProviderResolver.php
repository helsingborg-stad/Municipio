<?php

declare(strict_types=1);

namespace Municipio\Chat\Provider;

class ChatProviderResolver implements ChatProviderResolverInterface
{
    public function __construct(
        private ChatProviderInterface $aiChatProvider,
    ) {}

    public function resolve(array $assistant): ChatProviderInterface
    {
        return $this->aiChatProvider;
    }
}
