<?php

declare(strict_types=1);

namespace Municipio\Chat\Api\Providers;

use Municipio\Chat\Api\ChatEndpointHelpers;
use Municipio\Chat\Config\ChatConfigInterface;
use Municipio\Chat\PIIRedactor\RedactionResult;
use WpService\Contracts\__;

/**
 * AI-specific conversation provider responsible for mapping request payloads
 * and streaming SSE responses from the upstream assistant API.
 */
class AiConversationApiProvider implements ConversationApiProviderInterface
{
    /**
     * @param ChatConfigInterface $config Chat feature configuration provider.
     * @param __ $wpService WordPress translation contract.
     */
    public function __construct(
        private ChatConfigInterface $config,
        private __ $wpService,
    ) {
    }

    /**
     * @param array<string, mixed> $requestParams
     */
    public function createProviderRequest(array $requestParams, RedactionResult $redaction): mixed
    {
        $assistant = $this->resolveAssistant($requestParams);
        if ($this->isWpError($assistant)) {
            return $assistant;
        }

        $configError = $this->validateAssistantConfig($assistant);
        if ($this->isWpError($configError)) {
            return $configError;
        }

        return new ProviderRequest(
            chatUrl: $assistant['server_url'],
            apiKey: $assistant['api_key'],
            body: $this->buildRequestBody($requestParams, $assistant, $redaction),
        );
    }

    public function streamResponse(ProviderRequest $providerRequest): void
    {
        header('Content-Type: text/event-stream');
        header('Cache-Control: no-cache');
        header('Connection: keep-alive');
        header('X-Accel-Buffering: no');

        while (ob_get_level()) {
            ob_end_clean();
        }

        $curlHandle = curl_init($providerRequest->chatUrl);
        $buffer = '';

        try {
            curl_setopt_array($curlHandle, [
                CURLOPT_POST => true,
                CURLOPT_HTTPHEADER => [
                    'Content-Type: application/json',
                    'X-Api-Key: ' . $providerRequest->apiKey,
                ],
                CURLOPT_POSTFIELDS => json_encode($providerRequest->body),
                CURLOPT_RETURNTRANSFER => false,
                CURLOPT_WRITEFUNCTION => static function (\CurlHandle $_curlHandle, string $chunk) use (&$buffer): int {
                    $buffer .= $chunk;
                    $buffer = str_replace(["\r\n", "\r"], "\n", $buffer);

                    while (($eventEnd = strpos($buffer, "\n\n")) !== false) {
                        $event = substr($buffer, 0, $eventEnd);
                        $buffer = substr($buffer, $eventEnd + 2);

                        $trimmedEvent = ChatEndpointHelpers::trimEventPayload(
                            $event,
                            AiProtocolProfile::getValidSseEventNames(),
                            AiProtocolProfile::getValidSseResponseKeys(),
                        );

                        if ($trimmedEvent !== null) {
                            echo $trimmedEvent . "\n\n";
                            ob_flush();
                            flush();
                        }
                    }

                    return strlen($chunk);
                },
            ]);

            $success = curl_exec($curlHandle);

            if ($success === false || curl_errno($curlHandle) !== 0) {
                $this->logCurlError(curl_error($curlHandle));
                echo
                    "event: error\ndata: "
                        . json_encode([
                            'error' => $this->wpService->__('Failed to communicate with chat API.', 'municipio'),
                            'code' => AiProtocolProfile::COMMUNICATION_FAILURE_CODE,
                        ])
                        . "\n\n"
                ;
                ob_flush();
                flush();
            }
        } finally {
            curl_close($curlHandle);
        }
    }

    /**
     * @param array<string, mixed> $requestParams
    * @return array<string, mixed>|object
     */
    private function resolveAssistant(array $requestParams): array|object
    {
        $assistantUniqueId = $requestParams['assistant_name'] ?? null;

        if (!is_string($assistantUniqueId) || $assistantUniqueId === '' || $assistantUniqueId === 'Default') {
            return $this->config->getDefaultAssistant() ?? [];
        }

        $allAssistants = $this->config->getAssistants();

        foreach ($allAssistants as $candidate) {
            if (($candidate['name'] ?? null) === $assistantUniqueId) {
                return $candidate;
            }
        }

        return $this->createWpError(
            'chat_assistant_not_found',
            $this->wpService->__('Assistant not found.', 'municipio'),
            ['status' => 404],
        );
    }

    /**
     * @param array<string, mixed> $assistant
     */
    private function validateAssistantConfig(array $assistant): ?object
    {
        if (empty($assistant['server_url']) || empty($assistant['api_key']) || empty($assistant['assistant_id'])) {
            return $this->createWpError(
                'chat_assistant_incomplete',
                $this->wpService->__('Assistant configuration is incomplete.', 'municipio'),
                ['status' => 500],
            );
        }

        return null;
    }

    /**
     * @param array<string, mixed> $requestParams
     * @param array<string, mixed> $assistant
     * @return array<string, mixed>
     */
    private function buildRequestBody(array $requestParams, array $assistant, RedactionResult $redaction): array
    {
        $requestBody = [
            'question' => $redaction->redactedText,
            'stream' => true,
        ];

        $sessionId = $requestParams['session_id'] ?? null;

        if (is_string($sessionId) && $sessionId !== '') {
            $requestBody['session_id'] = $sessionId;
        } else {
            $requestBody['assistant_id'] = $assistant['assistant_id'];
        }

        return $requestBody;
    }

    /**
     * @param string $curlErrorMessage cURL error message.
     */
    private function logCurlError(string $curlErrorMessage): void
    {
        error_log(
            sprintf(
                '[AiConversationApiProvider] Chat API communication failed: %s',
                $curlErrorMessage,
            ),
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
}
