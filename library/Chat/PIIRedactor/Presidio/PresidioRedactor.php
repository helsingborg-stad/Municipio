<?php

namespace Municipio\Chat\PIIRedactor\Presidio;

use Municipio\Chat\PIIRedactor\Exception\PIIRedactionException;
use Municipio\Chat\PIIRedactor\PIIRedactorInterface;
use Municipio\Chat\PIIRedactor\RedactionResult;
use WpService\Contracts\IsWpError;
use WpService\Contracts\WpRemotePost;
use WpService\Contracts\WpRemoteRetrieveBody;
use WpService\Contracts\WpRemoteRetrieveResponseCode;

class PresidioRedactor implements PIIRedactorInterface
{
    public function __construct(
        private IsWpError&WpRemotePost&WpRemoteRetrieveBody&WpRemoteRetrieveResponseCode $wpService,
        private PresidioRedactorConfig $config,
    ) {}

    public function extractAndRedactPII(string $input): RedactionResult
    {
        $analyzerResults = $this->callAnalyze($input);
        $anonymized = $this->callAnonymize($input, $analyzerResults);

        $result = new RedactionResult();

        if (!isset($anonymized['text'])) {
            throw new PIIRedactionException('Invalid response from Presidio anonymizer: missing "text" field.');
        }

        $result->redactedText = $anonymized['text'];

        return $result;
    }

    private function callAnalyze(string $input): array
    {
        if (!isset($this->config->language) || empty($this->config->language)) {
            throw new PIIRedactionException('Unable to determine language for Presidio.');
        }

        if (empty($this->config->presidioAnalyzeHost)) {
            throw new PIIRedactionException('Presidio analyzer host is not configured.');
        }

        $fullUrl = rtrim($this->config->presidioAnalyzeHost, '/') . '/analyze';
        $response = $this->post($fullUrl, [
            'text' => $input,
            'language' => $this->config->language,
            'allow_list' => $this->config->allowList,
        ]);

        if (!is_array($response) || !array_is_list($response)) {
            throw new PIIRedactionException('Invalid response from Presidio analyzer.');
        }

        return $response;
    }

    private function callAnonymize(string $input, array $analyzerResults): array
    {
        if (empty($this->config->presidioAnonymizeHost)) {
            throw new PIIRedactionException('Presidio anonymizer host is not configured.');
        }

        $body = [
            'text' => $input,
            'analyzer_results' => $analyzerResults,
            'anonymizers' => (object) ($this->config->anonymizerConfig ?? []),
        ];

        $fullUrl = rtrim($this->config->presidioAnonymizeHost, '/') . '/anonymize';
        $response = $this->post($fullUrl, $body);
        return is_array($response) ? $response : [];
    }

    private function encodeRequestBody(array $body): string
    {
        try {
            return json_encode($body, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new PIIRedactionException(sprintf('Failed to encode request body for Presidio: %s', $e->getMessage()));
        }
    }

    private function post(string $url, array $body): mixed
    {
        $encoded_body = $this->encodeRequestBody($body);
        $response = $this->wpService->wpRemotePost($url, [
            'headers' => ['Content-Type' => 'application/json'],
            'body' => $encoded_body,
            'timeout' => 10,
        ]);

        if ($this->wpService->isWpError($response)) {
            throw new PIIRedactionException(sprintf(
                'Presidio request to %s failed: %s.',
                $url,
                $response->get_error_message(),
            ));
        }

        $status = $this->wpService->wpRemoteRetrieveResponseCode($response);
        if ($status < 200 || $status >= 300) {
            throw new PIIRedactionException(sprintf(
                'Presidio request to %s returned HTTP %d',
                $url,
                $status,
            ));
        }

        return json_decode($this->wpService->wpRemoteRetrieveBody($response), true);
    }
}
