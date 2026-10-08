<?php

declare(strict_types=1);

namespace Municipio\Chat\Provider;

class ChatProviderResolver implements ChatProviderResolverInterface
{
    public function __construct(
        private ChatProviderInterface $aiChatProvider,
    ) {}

    public function resolve(array $assistant): ChatProviderInterface|\WP_Error
    {
        $providerType = strtolower((string) ($assistant['provider_type'] ?? 'ai'));

        if ($providerType === '' || $providerType === 'ai') {
            return $this->aiChatProvider;
        }

        return new \WP_Error(
            'chat_provider_not_supported',
            __('Chat provider is not supported.', 'municipio'),
            ['status' => 400],
        );
    }
}
