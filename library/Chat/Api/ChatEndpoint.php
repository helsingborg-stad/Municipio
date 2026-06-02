<?php

declare(strict_types=1);


namespace Municipio\Chat\Api;

use Municipio\Api\RestApiEndpoint;
use Municipio\Chat\Config\ChatConfigInterface;
use Municipio\Chat\PIIRedactor\PIIRedactorInterface;
use Municipio\Chat\PIIRedactor\RedactionResult;
use WpService\Contracts\RegisterRestRoute;

class ChatEndpoint extends RestApiEndpoint
{
    private const NAMESPACE = 'municipio/v1';
    private const ROUTE = '/chat';

    public function __construct(
        private ChatConfigInterface $config,
        private PIIRedactorInterface $piiRedactor,
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

        $configError = $this->validateAssistantConfig($assistant);
        if ($configError instanceof \WP_Error) {
            return $configError;
        }

        $redaction = $this->redactMessage(sanitize_text_field($params['message']));
        if ($redaction instanceof \WP_Error) {
            return $redaction;
        }

        $body = $this->buildRequestBody($params, $assistant, $redaction);

        $this->registerSseStream(
            $request,
            $assistant['server_url'],
            $assistant['api_key'],
            $body,
        );

        return rest_ensure_response(null);
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

    private function validateAssistantConfig(array $assistant): ?\WP_Error
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

    private function redactMessage(string $message): RedactionResult|\WP_Error
    {
        try {
            return $this->piiRedactor->extractAndRedactPII($message);
        } catch (\Throwable $e) {
            $this->logRedactionError($e);
            return new \WP_Error(
                'chat_pii_redaction_failed',
                __('Unable to process message safely. Please try again later.', 'municipio'),
                ['status' => 503],
            );
        }
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

    private function registerSseStream(
        \WP_REST_Request $request,
        string $chatUrl,
        #[\SensitiveParameter] string $apiKey,
        array $body,
    ): void {
        add_filter(
            'rest_pre_serve_request',
            function ($served, $result, $filterRequest) use ($chatUrl, $apiKey, $body, $request) {
                if ($filterRequest !== $request) {
                    return $served;
                }
                $this->streamResponse($chatUrl, $apiKey, $body);
                return true;
            },
            10,
            3,
        );
    }

    private function streamResponse(string $chatUrl, #[\SensitiveParameter] string $apiKey, array $body): void
    {
        header('Content-Type: text/event-stream');
        header('Cache-Control: no-cache');
        header('Connection: keep-alive');
        header('X-Accel-Buffering: no'); // Disables nginx buffering

        while (ob_get_level()) {
            ob_end_clean();
        }

        $ch = curl_init($chatUrl);

        try {
            curl_setopt_array($ch, [
                CURLOPT_POST => true,
                CURLOPT_HTTPHEADER => [
                    'Content-Type: application/json',
                    'X-Api-Key: ' . $apiKey,
                ],
                CURLOPT_POSTFIELDS => json_encode($body),
                CURLOPT_RETURNTRANSFER => false,
                CURLOPT_WRITEFUNCTION => static function ($ch, $data) {
                    echo $data . "\n\n";
                    ob_flush();
                    flush();
                    return \strlen($data);
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
                '[ChatEndpoint] Chat API communication failed: %s',
                $curlErrorMessage,
            ),
        );
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
