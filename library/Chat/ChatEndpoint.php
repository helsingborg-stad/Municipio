<?php

namespace Municipio\Chat;

use AcfService\Contracts\GetField;
use Municipio\Api\RestApiEndpoint;
use Municipio\Chat\PIIRedactor\PIIRedactorInterface;

class ChatEndpoint extends RestApiEndpoint
{
    private const NAMESPACE = 'municipio/v1';
    private const ROUTE = '/chat';

    public function __construct(
        private GetField $acfService,
        private PIIRedactorInterface $PIIRedactor,
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

        $assistantUniqueId = $params['assistant_id'] ?? null;

        if (!isset($assistantUniqueId) || empty($assistantUniqueId)) {
            $assistantUniqueId = $this->acfService->getField('chat_default_assistant', 'option');
        }

        $allAssistants = $this->acfService->getField('chat_assistants', 'option') ?? [];
        $assistant = null;
        foreach ($allAssistants as $a) {
            if ($a['id'] === $assistantUniqueId) {
                $assistant = $a;
                break;
            }
        }

        if (!$assistant) {
            return rest_ensure_response(['error' => 'Assistant not found.']);
        }

        $chatUrl = $assistant['server_url'] ?? null;
        $apiKey = $assistant['api_key'] ?? null;
        $remoteAssistantId = $assistant['assistant_id'] ?? null;

        if (!$chatUrl || !$apiKey || !$remoteAssistantId) {
            return rest_ensure_response(['error' => 'Assistant configuration is incomplete.']);
        }

        $sessionId = $params['session_id'] ?? null;

        // Clean-up user message
        $message = sanitize_text_field($params['message']);
        $redaction = $this->PIIRedactor->extractAndRedactPII($message);

        // Perform a POST request to the external chat API
        $body = [
            'question' => $redaction->redactedText,
            'stream' => true,
        ];

        if ($sessionId) {
            $body['session_id'] = $sessionId;
        } else {
            $body['assistant_id'] = $remoteAssistantId;
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

        // TODO: test client error handling (missing/invalid API key, token limits, etc.)

        if (curl_error($ch)) {
            echo 'event: error\ndata: ' . json_encode(['error' => 'Failed to communicate with chat API.', 'details' => curl_error($ch)]) . "\n\n";
            ob_flush();
            flush();
        }

        exit();
    }
}
