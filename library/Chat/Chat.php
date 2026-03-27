<?php

namespace Municipio\Chat;

use AcfService\Contracts\GetField;
use Municipio\Api\RestApiEndpoint;

class Chat extends RestApiEndpoint
{
    private const NAMESPACE = 'municipio/v1';
    private const ROUTE = '/chat';

    public function __construct(
        private GetField $acfService,
    ) {}

    public function handleRegisterRestRoute(): bool
    {
        return register_rest_route(self::NAMESPACE, self::ROUTE, array(
            'methods' => 'POST',
            'callback' => array($this, 'handleRequest'),
            'permission_callback' => '__return_true',
        ));
    }

    public function handleRequest(\WP_REST_Request $request): \WP_REST_Response
    {
        $params = $request->get_params();

        if (!isset($params['message']) || empty($params['message'])) {
            return rest_ensure_response(['error' => 'No message provided.']);
        }

        $sessionId = $params['session_id'] ?? null;

        // Clean-up user message
        $message = sanitize_text_field($params['message']);
        $message = $this->redactPII($message);

        // Perform a POST request to the external chat API
        $chatUrl = $this->acfService->getField('chat_url', 'option');
        $apiKey = $this->acfService->getField('chat_api_key', 'option');
        $assistandId = $this->acfService->getField('chat_assistant_id', 'option');

        $body = [
            'question' => $message,
            'stream' => true,
        ];

        if ($sessionId) {
            $body['session_id'] = $sessionId;
        } else {
            $body['assistant_id'] = $assistandId;
        }

        // Set SSE headers
        header('Content-Type: text/event-stream');
        header('Cache-Control: no-cache');
        header('Connection: keep-alive');
        header('X-Accel-Buffering: no'); // Disables nginx buffering

        // Disable PHP output buffering
        while (ob_get_level()) {
            ob_end_clean();
        }

        $ch = curl_init($chatUrl);
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'X-Api-Key: ' . $apiKey,
            ],
            CURLOPT_POSTFIELDS => json_encode($body),
            CURLOPT_RETURNTRANSFER => false,
            CURLOPT_WRITEFUNCTION => function ($ch, $data) {
                echo $data . "\n\n";
                ob_flush();
                flush();
                return \strlen($data);
            },
        ]);

        curl_exec($ch);

        // TODO: test errors (missing/invalid API key, token limits, etc.)

        if (curl_error($ch)) {
            echo 'event: error\ndata: ' . json_encode(['error' => 'Failed to communicate with chat API.', 'details' => curl_error($ch)]) . "\n\n";
            ob_flush();
            flush();
        }

        exit();
    }

    private function redactPII(string $text): string
    {
        // TODO: use AI-based PPI detection for better accuracy and flexibility

        // Simple regex to match email addresses and phone numbers (basic example)
        $emailPattern = '/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/';
        $phonePattern = '/\b\d{3}[-.\s]??\d{3}[-.\s]??\d{4}\b/';

        // Replace matches with [REDACTED]
        $redactedText = preg_replace($emailPattern, '[REDACTED]', $text);
        $redactedText = preg_replace($phonePattern, '[REDACTED]', $redactedText);

        return $redactedText;
    }
}
