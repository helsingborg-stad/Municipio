<?php

declare(strict_types=1);

namespace Municipio\Chat\Api;

use Municipio\Api\RestApiEndpoint;
use Municipio\Chat\Api\Providers\ConversationApiProviderFactoryInterface;
use Municipio\Chat\Api\Providers\ConversationApiProviderInterface;
use Municipio\Chat\Api\Providers\ProviderRequest;
use Municipio\Chat\Config\ChatConfigInterface;
use Municipio\Chat\PIIRedactor\PIIRedactorFactoryInterface;
use Municipio\Chat\PIIRedactor\RedactionResult;
use WpService\Contracts\__;
use WpService\Contracts\AddFilter;
use WpService\Contracts\RegisterRestRoute;
use WpService\Contracts\RestEnsureResponse;
use WpService\Contracts\SanitizeTextField;

class ChatEndpoint extends RestApiEndpoint
{
    private const NAMESPACE = 'municipio/v1';
    private const ROUTE = '/chat';

    public function __construct(
        private ChatConfigInterface $config,
        private PIIRedactorFactoryInterface $piiRedactorFactory,
        private ConversationApiProviderFactoryInterface $conversationProviderFactory,
        private RegisterRestRoute&AddFilter&SanitizeTextField&RestEnsureResponse&__ $wpService,
    ) {}

    public function handleRegisterRestRoute(): bool
    {
        return $this->wpService->registerRestRoute(self::NAMESPACE, self::ROUTE, [
            'methods' => 'POST',
            'callback' => [$this, 'handleRequest'],
            'permission_callback' => '__return_true',
        ]);
    }

    public function handleRequest(object $request): object
    {
        $params = $request->get_params();
        $messageError = $this->validateMessage($params);
        if ($this->isWpError($messageError)) {
            return $messageError;
        }

        $redaction = $this->redactMessage($this->wpService->sanitizeTextField((string) $params['message']));
        if ($this->isWpError($redaction)) {
            return $redaction;
        }

        $conversationProvider = $this->conversationProviderFactory->createProvider($params);

        $providerRequest = $conversationProvider->createProviderRequest($params, $redaction);
        if ($this->isWpError($providerRequest)) {
            return $providerRequest;
        }

        $this->registerSseStream($request, $providerRequest, $conversationProvider);

        return $this->wpService->restEnsureResponse(null);
    }

    /**
     * @param array<string, mixed> $params
     */
    private function validateMessage(array $params): ?object
    {
        if (!isset($params['message']) || empty($params['message'])) {
            return $this->createWpError(
                'chat_message_missing',
                $this->wpService->__('No message provided.', 'municipio'),
                ['status' => 400],
            );
        }

        return null;
    }

    private function redactMessage(string $message): mixed
    {
        try {
            return $this->piiRedactorFactory->create($this->config)->extractAndRedactPII($message);
        } catch (\Throwable $e) {
            $this->logRedactionError($e);
            return $this->createWpError(
                'chat_pii_redaction_failed',
                $this->wpService->__('Unable to process message safely. Please try again later.', 'municipio'),
                ['status' => 503],
            );
        }
    }

    private function registerSseStream(
        object $request,
        ProviderRequest $providerRequest,
        ConversationApiProviderInterface $conversationProvider,
    ): void {
        $this->wpService->addFilter(
            'rest_pre_serve_request',
            function ($served, $result, $filterRequest) use ($providerRequest, $request, $conversationProvider) {
                if ($filterRequest !== $request) {
                    return $served;
                }

                $conversationProvider->streamResponse($providerRequest);
                return true;
            },
            10,
            3,
        );
    }

    /**
     * @param mixed $value
     */
    private function isWpError(mixed $value): bool
    {
        return is_object($value) && is_a($value, 'WP_Error');
    }

    /**
     * @param array<string, mixed> $data
     */
    private function createWpError(string $code, string $message, array $data): object
    {
        $errorClass = 'WP_Error';
        return new $errorClass($code, $message, $data);
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
