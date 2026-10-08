<?php

declare(strict_types=1);

namespace Municipio\Chat\Api;

use Municipio\Api\RestApiEndpoint;
use Municipio\Chat\Config\ChatConfigInterface;
use Municipio\Chat\PIIRedactor\PIIRedactorFactoryInterface;
use Municipio\Chat\PIIRedactor\RedactionResult;
use Municipio\Chat\Provider\ChatProviderResolverInterface;
use WpService\Contracts\RegisterRestRoute;

class ChatEndpoint extends RestApiEndpoint
{
    private const NAMESPACE = 'municipio/v1';
    private const ROUTE = '/chat';

    public function __construct(
        private ChatConfigInterface $config,
        private PIIRedactorFactoryInterface $piiRedactorFactory,
        private ChatProviderResolverInterface $providerResolver,
        private RegisterRestRoute $wpService,
    ) {}

    public function handleRegisterRestRoute(): bool
    {
        return $this->wpService->registerRestRoute(self::NAMESPACE, self::ROUTE, [
            'methods' => 'POST',
            'callback' => [$this, 'handleRequest'],
            'permission_callback' => '__return_true',
        ]);
    }

    public function handleRequest(\WP_REST_Request $request): \WP_REST_Response|\WP_Error
    {
        $params = $request->get_params();
        $messageError = $this->validateMessage($params);
        if ($messageError instanceof \WP_Error) {
            return $messageError;
        }

        $assistant = $this->resolveAssistant($params);
        if ($assistant instanceof \WP_Error) {
            return $assistant;
        }

        $provider = $this->providerResolver->resolve($assistant);
        if ($provider instanceof \WP_Error) {
            return $provider;
        }

        $configError = $provider->validateAssistantConfig($assistant);
        if ($configError instanceof \WP_Error) {
            return $configError;
        }

        $redaction = $this->redactMessage((string) sanitize_text_field((string) $params['message']));
        if ($redaction instanceof \WP_Error) {
            return $redaction;
        }

        $provider->registerSseStream(
            $request,
            $assistant,
            $params,
            $redaction,
        );

        return new \WP_REST_Response(null);
    }

    private function validateMessage(array $params): ?\WP_Error
    {
        if (!isset($params['message']) || empty($params['message'])) {
            return new \WP_Error(
                'chat_message_missing',
                __('No message provided.', 'municipio'),
                ['status' => 400],
            );
        }

        return null;
    }

    private function resolveAssistant(array $params): array|\WP_Error
    {
        $assistantUniqueId = $params['assistant_name'] ?? null;

        if (empty($assistantUniqueId) || $assistantUniqueId === 'Default') {
            return $this->config->getDefaultAssistant() ?? [];
        }

        $allAssistants = $this->config->getAssistants();

        foreach ($allAssistants as $candidate) {
            if ($candidate['name'] === $assistantUniqueId) {
                return $candidate;
            }
        }

        return new \WP_Error(
            'chat_assistant_not_found',
            __('Assistant not found.', 'municipio'),
            ['status' => 404],
        );
    }

    private function redactMessage(string $message): RedactionResult|\WP_Error
    {
        try {
            return $this->piiRedactorFactory->create($this->config)->extractAndRedactPII($message);
        } catch (\Throwable $e) {
            $this->logRedactionError($e);
            return new \WP_Error(
                'chat_pii_redaction_failed',
                __('Unable to process message safely. Please try again later.', 'municipio'),
                ['status' => 503],
            );
        }
    }

    private function logRedactionError(\Throwable $error): void
    {
        error_log(
            sprintf(
                '[ChatEndpoint] PII redaction failed: %s',
                $error->getMessage(),
            ),
        );
    }
}
