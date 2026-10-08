<?php

declare(strict_types=1);

namespace Municipio\Chat\Provider;

use Municipio\Chat\Api\ChatEndpointHelpers;
use Municipio\Chat\PIIRedactor\RedactionResult;

class AiChatProvider implements ChatProviderInterface
{
    private const VALID_SSE_EVENT_NAMES = ['first_chunk', 'text', 'tool_call', 'error'];
    private const VALID_SSE_RESPONSE_KEYS = ['session_id', 'answer', 'error'];

    public function validateAssistantConfig(array $assistant): ?\WP_Error
    {
        if (empty($assistant['server_url']) || empty($assistant['api_key']) || empty($assistant['assistant_id'])) {
            return new \WP_Error(
                'chat_assistant_incomplete',
                __('Assistant configuration is incomplete.', 'municipio'),
                ['status' => 500],
            );
        }

        return null;
    }

    public function registerSseStream(
        \WP_REST_Request $request,
        array $assistant,
        array $params,
        RedactionResult $redaction,
    ): void {
        $body = $this->buildRequestBody($params, $assistant, $redaction);

        \add_filter(
            'rest_pre_serve_request',
            function ($served, $result, $filterRequest) use ($assistant, $body, $request) {
                if ($filterRequest !== $request) {
                    return $served;
                }

                $this->streamResponse($assistant['server_url'], $assistant['api_key'], $body);
                return true;
            },
            10,
            3,
        );
    }

    private function buildRequestBody(array $params, array $assistant, RedactionResult $redaction): array
    {
        $body = [
            'question' => $redaction->redactedText,
            'stream' => true,
        ];

        $sessionId = $params['session_id'] ?? null;
        if ($sessionId) {
            $body['session_id'] = $sessionId;
        } else {
            $body['assistant_id'] = $assistant['assistant_id'];
        }

        return $body;
    }

    private function streamResponse(string $chatUrl, #[\SensitiveParameter] string $apiKey, array $body): void
    {
        header('Content-Type: text/event-stream');
        header('Cache-Control: no-cache');
        header('Connection: keep-alive');
        header('X-Accel-Buffering: no');

        while (ob_get_level()) {
            ob_end_clean();
        }

        $ch = curl_init($chatUrl);
        $accum = '';

        try {
            curl_setopt_array($ch, [
                CURLOPT_POST => true,
                CURLOPT_HTTPHEADER => [
                    'Content-Type: application/json',
                    'X-Api-Key: ' . $apiKey,
                ],
                CURLOPT_POSTFIELDS => json_encode($body),
                CURLOPT_RETURNTRANSFER => false,
                CURLOPT_WRITEFUNCTION => static function (\CurlHandle $_ch, string $data) use (&$accum): int {
                    $accum .= $data;
                    $accum = str_replace(["\r\n", "\r"], "\n", $accum);

                    while (($eventEnd = strpos($accum, "\n\n")) !== false) {
                        $event = substr($accum, 0, $eventEnd);
                        $accum = substr($accum, $eventEnd + 2);

                        $trimmed = ChatEndpointHelpers::trimEventPayload(
                            $event,
                            self::VALID_SSE_EVENT_NAMES,
                            self::VALID_SSE_RESPONSE_KEYS,
                        );

                        if ($trimmed !== null) {
                            echo $trimmed . "\n\n";
                            ob_flush();
                            flush();
                        }
                    }

                    return strlen($data);
                },
            ]);

            $success = curl_exec($ch);

            if ($success === false || curl_errno($ch) !== 0) {
                $this->logCurlError(curl_error($ch));
                echo
                    "event: error\ndata: "
                        . json_encode([
                            'error' => __('Failed to communicate with chat API.', 'municipio'),
                            'code' => 'chat_api_communication_failed',
                        ])
                        . "\n\n"
                ;
                ob_flush();
                flush();
            }
        } finally {
            curl_close($ch);
        }
    }

    private function logCurlError(string $curlErrorMessage): void
    {
        error_log(
            sprintf(
                '[AiChatProvider] Chat API communication failed: %s',
                $curlErrorMessage,
            ),
        );
    }
}
