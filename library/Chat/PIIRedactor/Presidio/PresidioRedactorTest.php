<?php

namespace Municipio\Chat\PIIRedactor\Presidio;

use Municipio\Chat\PIIRedactor\Exception\PIIRedactionException;
use Municipio\Chat\PIIRedactor\PIIRedactorInterface;
use Municipio\Chat\PIIRedactor\RedactionResult;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class PresidioRedactorTest extends TestCase
{
    #[TestDox('class can be instantiated')]
    public function testClassCanBeInstantiated(): void
    {
        $redactor = new PresidioRedactor(new FakeWpService(), new PresidioRedactorConfig('https://presidio.example.com', 'https://presidio.example.com'));

        $this->assertInstanceOf(PresidioRedactor::class, $redactor);
    }

    #[TestDox('extractAndRedactPII() returns a RedactionResult with the anonymized text')]
    public function testExtractAndRedactPIIReturnsRedactionResult(): void
    {
        $wpService = $this->getWpService([
            'analyze' => $this->successResponse('[]'),
            'anonymize' => $this->successResponse(json_encode(['text' => 'Hello <PERSON>'])),
        ]);
        $redactor = new PresidioRedactor($wpService, new PresidioRedactorConfig('https://presidio.example.com', 'https://presidio.example.com', 'en'));

        $result = $redactor->extractAndRedactPII('Hello John');

        $this->assertInstanceOf(RedactionResult::class, $result);
        $this->assertSame('Hello <PERSON>', $result->redactedText);
    }

    #[TestDox('extractAndRedactPII() implements PIIRedactorInterface')]
    public function testImplementsPIIRedactorInterface(): void
    {
        $redactor = new PresidioRedactor(new FakeWpService(), new PresidioRedactorConfig('https://presidio.example.com', 'https://presidio.example.com'));

        $this->assertInstanceOf(PIIRedactorInterface::class, $redactor);
    }

    #[TestDox('extractAndRedactPII() posts the input text and language to the analyze endpoint')]
    public function testPostsTextAndLanguageToAnalyzeEndpoint(): void
    {
        $wpService = $this->getWpService([
            'analyze' => $this->successResponse('[]'),
            'anonymize' => $this->successResponse(json_encode(['text' => 'redacted'])),
        ]);
        $redactor = new PresidioRedactor($wpService, new PresidioRedactorConfig('https://presidio.example.com', 'https://presidio.example.com', 'sv'));

        $redactor->extractAndRedactPII('Hello John');

        $analyzeCall = $this->findCallTo($wpService, 'https://presidio.example.com/analyze');
        $this->assertNotNull($analyzeCall);
        $body = json_decode($analyzeCall[1]['body'], true);
        $this->assertSame('Hello John', $body['text']);
        $this->assertSame('sv', $body['language']);
    }

    #[TestDox('extractAndRedactPII() posts the configured allow list to the analyze endpoint')]
    public function testPostsAllowListToAnalyzeEndpoint(): void
    {
        $wpService = $this->getWpService([
            'analyze' => $this->successResponse('[]'),
            'anonymize' => $this->successResponse(json_encode(['text' => 'redacted'])),
        ]);
        $allowList = ['Helsingborg', 'Stockholm'];
        $redactor = new PresidioRedactor(
            $wpService,
            new PresidioRedactorConfig('https://presidio.example.com', 'https://presidio.example.com', 'en', null, $allowList),
        );

        $redactor->extractAndRedactPII('Hello');

        $analyzeCall = $this->findCallTo($wpService, 'https://presidio.example.com/analyze');
        $body = json_decode($analyzeCall[1]['body'], true);
        $this->assertSame($allowList, $body['allow_list']);
    }

    #[TestDox('extractAndRedactPII() posts an empty allow list to the analyze endpoint by default')]
    public function testPostsEmptyAllowListByDefault(): void
    {
        $wpService = $this->getWpService([
            'analyze' => $this->successResponse('[]'),
            'anonymize' => $this->successResponse(json_encode(['text' => 'redacted'])),
        ]);
        $redactor = new PresidioRedactor($wpService, new PresidioRedactorConfig('https://presidio.example.com', 'https://presidio.example.com', 'en'));

        $redactor->extractAndRedactPII('Hello');

        $analyzeCall = $this->findCallTo($wpService, 'https://presidio.example.com/analyze');
        $this->assertStringContainsString('"allow_list":[]', $analyzeCall[1]['body']);
    }

    #[TestDox('extractAndRedactPII() posts the input text, analyzer results, and anonymizer config to the anonymize endpoint')]
    public function testPostsAnalyzerResultsAndConfigToAnonymizeEndpoint(): void
    {
        $analyzerResults = [['entity_type' => 'PERSON', 'start' => 6, 'end' => 10, 'score' => 0.9]];
        $wpService = $this->getWpService([
            'analyze' => $this->successResponse(json_encode($analyzerResults)),
            'anonymize' => $this->successResponse(json_encode(['text' => 'redacted'])),
        ]);
        $config = ['DEFAULT' => ['type' => 'replace', 'new_value' => '<X>']];
        $redactor = new PresidioRedactor($wpService, new PresidioRedactorConfig('https://presidio.example.com', 'https://presidio.example.com', 'sv', $config));

        $redactor->extractAndRedactPII('Hello John');

        $anonymizeCall = $this->findCallTo($wpService, 'https://presidio.example.com/anonymize');
        $this->assertNotNull($anonymizeCall);
        $body = json_decode($anonymizeCall[1]['body'], true);
        $this->assertSame('Hello John', $body['text']);
        $this->assertSame($analyzerResults, $body['analyzer_results']);
        $this->assertSame($config, $body['anonymizers']);
    }

    #[TestDox('extractAndRedactPII() sends an empty object for anonymizers when no config is provided')]
    public function testSendsEmptyAnonymizersObjectWhenNoConfig(): void
    {
        $wpService = $this->getWpService([
            'analyze' => $this->successResponse('[]'),
            'anonymize' => $this->successResponse(json_encode(['text' => 'redacted'])),
        ]);
        $redactor = new PresidioRedactor($wpService, new PresidioRedactorConfig('https://presidio.example.com', 'https://presidio.example.com', 'en'));

        $redactor->extractAndRedactPII('Hello');

        $anonymizeCall = $this->findCallTo($wpService, 'https://presidio.example.com/anonymize');
        $this->assertStringContainsString('"anonymizers":{}', $anonymizeCall[1]['body']);
    }

    #[TestDox('extractAndRedactPII() uses a separate anonymize host when configured')]
    public function testUsesSeparateAnonymizeHost(): void
    {
        $wpService = $this->getWpService([
            'analyze' => $this->successResponse('[]'),
            'anonymize' => $this->successResponse(json_encode(['text' => 'redacted'])),
        ]);
        $redactor = new PresidioRedactor(
            $wpService,
            new PresidioRedactorConfig(
                'https://analyzer.example.com',
                'https://anonymizer.example.com',
                'en',
            ),
        );

        $redactor->extractAndRedactPII('Hello');

        $this->assertNotNull($this->findCallTo($wpService, 'https://analyzer.example.com/analyze'));
        $this->assertNotNull($this->findCallTo($wpService, 'https://anonymizer.example.com/anonymize'));
    }

    #[TestDox('extractAndRedactPII() throws a PIIRedactionException when the analyze host is null')]
    public function testThrowsWhenAnalyzeHostIsNull(): void
    {
        $wpService = $this->getWpService([
            'analyze' => $this->successResponse('[]'),
            'anonymize' => $this->successResponse(json_encode(['text' => 'redacted'])),
        ]);
        $redactor = new PresidioRedactor($wpService, new PresidioRedactorConfig(null, 'https://anonymizer.example.com', 'en'));

        $this->expectException(PIIRedactionException::class);
        $redactor->extractAndRedactPII('Hello');
    }

    #[TestDox('extractAndRedactPII() throws a PIIRedactionException when the analyze host is an empty string')]
    public function testThrowsWhenAnalyzeHostIsEmptyString(): void
    {
        $wpService = $this->getWpService([
            'analyze' => $this->successResponse('[]'),
            'anonymize' => $this->successResponse(json_encode(['text' => 'redacted'])),
        ]);
        $redactor = new PresidioRedactor($wpService, new PresidioRedactorConfig('', 'https://anonymizer.example.com', 'en'));

        $this->expectException(PIIRedactionException::class);
        $redactor->extractAndRedactPII('Hello');
    }

    #[TestDox('extractAndRedactPII() throws a PIIRedactionException when the anonymize host is null')]
    public function testThrowsWhenAnonymizeHostIsNull(): void
    {
        $wpService = $this->getWpService([
            'analyze' => $this->successResponse('[]'),
            'anonymize' => $this->successResponse(json_encode(['text' => 'redacted'])),
        ]);
        $redactor = new PresidioRedactor($wpService, new PresidioRedactorConfig('https://analyzer.example.com', null, 'en'));

        $this->expectException(PIIRedactionException::class);
        $redactor->extractAndRedactPII('Hello');
    }

    #[TestDox('extractAndRedactPII() throws a PIIRedactionException when the anonymize host is an empty string')]
    public function testThrowsWhenAnonymizeHostIsEmptyString(): void
    {
        $wpService = $this->getWpService([
            'analyze' => $this->successResponse('[]'),
            'anonymize' => $this->successResponse(json_encode(['text' => 'redacted'])),
        ]);
        $redactor = new PresidioRedactor($wpService, new PresidioRedactorConfig('https://analyzer.example.com', '', 'en'));

        $this->expectException(PIIRedactionException::class);
        $redactor->extractAndRedactPII('Hello');
    }

    #[TestDox('extractAndRedactPII() strips a trailing slash from the analyze and anonymize hosts')]
    public function testStripsTrailingSlashFromHosts(): void
    {
        $wpService = $this->getWpService([
            'analyze' => $this->successResponse('[]'),
            'anonymize' => $this->successResponse(json_encode(['text' => 'redacted'])),
        ]);
        $redactor = new PresidioRedactor(
            $wpService,
            new PresidioRedactorConfig(
                'https://analyzer.example.com/',
                'https://anonymizer.example.com/',
                'en',
            ),
        );

        $redactor->extractAndRedactPII('Hello');

        $this->assertNotNull($this->findCallTo($wpService, 'https://analyzer.example.com/analyze'));
        $this->assertNotNull($this->findCallTo($wpService, 'https://anonymizer.example.com/anonymize'));
    }

    #[TestDox('extractAndRedactPII() throws a PIIRedactionException when the analyze request returns a WP_Error')]
    public function testThrowsWhenAnalyzeReturnsWpError(): void
    {
        $wpService = $this->getWpService([
            'analyze' => new \WP_Error('http_request_failed', 'connection refused'),
            'anonymize' => $this->successResponse(json_encode(['text' => 'redacted'])),
        ]);
        $redactor = new PresidioRedactor($wpService, new PresidioRedactorConfig('https://presidio.example.com', 'https://presidio.example.com'));

        $this->expectException(PIIRedactionException::class);
        $redactor->extractAndRedactPII('Hello');
    }

    #[TestDox('extractAndRedactPII() throws a PIIRedactionException when the anonymize request returns a WP_Error')]
    public function testThrowsWhenAnonymizeReturnsWpError(): void
    {
        $wpService = $this->getWpService([
            'analyze' => $this->successResponse('[]'),
            'anonymize' => new \WP_Error('http_request_failed', 'connection refused'),
        ]);
        $redactor = new PresidioRedactor($wpService, new PresidioRedactorConfig('https://presidio.example.com', 'https://presidio.example.com'));

        $this->expectException(PIIRedactionException::class);
        $redactor->extractAndRedactPII('Hello');
    }

    #[TestDox('extractAndRedactPII() throws a PIIRedactionException when the analyze request returns a non-2xx status')]
    public function testThrowsWhenAnalyzeReturnsNon2xxStatus(): void
    {
        $wpService = $this->getWpService([
            'analyze' => ['status' => 500, 'body' => ''],
            'anonymize' => $this->successResponse(json_encode(['text' => 'redacted'])),
        ]);
        $redactor = new PresidioRedactor($wpService, new PresidioRedactorConfig('https://presidio.example.com', 'https://presidio.example.com'));

        $this->expectException(PIIRedactionException::class);
        $redactor->extractAndRedactPII('Hello');
    }

    #[TestDox('extractAndRedactPII() throws a PIIRedactionException when the anonymize request returns a non-2xx status')]
    public function testThrowsWhenAnonymizeReturnsNon2xxStatus(): void
    {
        $wpService = $this->getWpService([
            'analyze' => $this->successResponse('[]'),
            'anonymize' => ['status' => 500, 'body' => ''],
        ]);
        $redactor = new PresidioRedactor($wpService, new PresidioRedactorConfig('https://presidio.example.com', 'https://presidio.example.com'));

        $this->expectException(PIIRedactionException::class);
        $redactor->extractAndRedactPII('Hello');
    }

    #[TestDox('extractAndRedactPII() throws a PIIRedactionException when the anonymize response is missing the "text" field (fail-closed)')]
    public function testThrowsWhenAnonymizeResponseMissingTextField(): void
    {
        $wpService = $this->getWpService([
            'analyze' => $this->successResponse('[]'),
            'anonymize' => $this->successResponse(json_encode(['no_text' => 'oops'])),
        ]);
        $redactor = new PresidioRedactor($wpService, new PresidioRedactorConfig('https://presidio.example.com', 'https://presidio.example.com'));

        $this->expectException(PIIRedactionException::class);
        $redactor->extractAndRedactPII('Hello');
    }

    #[TestDox('extractAndRedactPII() throws a PIIRedactionException when the anonymize response body is invalid JSON (fail-closed)')]
    public function testThrowsWhenAnonymizeResponseIsInvalidJson(): void
    {
        $wpService = $this->getWpService([
            'analyze' => $this->successResponse('[]'),
            'anonymize' => $this->successResponse('not-json'),
        ]);
        $redactor = new PresidioRedactor($wpService, new PresidioRedactorConfig('https://presidio.example.com', 'https://presidio.example.com'));

        $this->expectException(PIIRedactionException::class);
        $redactor->extractAndRedactPII('Hello');
    }

    #[TestDox('extractAndRedactPII() throws a PIIRedactionException when the language is null')]
    public function testThrowsWhenLanguageIsNull(): void
    {
        $wpService = $this->getWpService([
            'analyze' => $this->successResponse('[]'),
            'anonymize' => $this->successResponse(json_encode(['text' => 'redacted'])),
        ]);
        $redactor = new PresidioRedactor($wpService, new PresidioRedactorConfig('https://presidio.example.com', 'https://presidio.example.com', null));

        $this->expectException(PIIRedactionException::class);
        $redactor->extractAndRedactPII('Hello');
    }

    #[TestDox('extractAndRedactPII() throws a PIIRedactionException when the language is an empty string')]
    public function testThrowsWhenLanguageIsEmptyString(): void
    {
        $wpService = $this->getWpService([
            'analyze' => $this->successResponse('[]'),
            'anonymize' => $this->successResponse(json_encode(['text' => 'redacted'])),
        ]);
        $redactor = new PresidioRedactor($wpService, new PresidioRedactorConfig('https://presidio.example.com', 'https://presidio.example.com', ''));

        $this->expectException(PIIRedactionException::class);
        $redactor->extractAndRedactPII('Hello');
    }

    #[TestDox('extractAndRedactPII() throws a PIIRedactionException when the analyze response is not a JSON array (fail-closed)')]
    public function testThrowsWhenAnalyzeResponseIsNotArray(): void
    {
        $wpService = $this->getWpService([
            'analyze' => $this->successResponse('"unexpected"'),
            'anonymize' => $this->successResponse(json_encode(['text' => 'Hello'])),
        ]);
        $redactor = new PresidioRedactor($wpService, new PresidioRedactorConfig('https://presidio.example.com', 'https://presidio.example.com', 'en'));

        $this->expectException(PIIRedactionException::class);
        $redactor->extractAndRedactPII('Hello');
    }

    #[TestDox('extractAndRedactPII() throws a PIIRedactionException when the analyze response is an associative array rather than a list (fail-closed)')]
    public function testThrowsWhenAnalyzeResponseIsAssociativeArray(): void
    {
        $wpService = $this->getWpService([
            'analyze' => $this->successResponse(json_encode(['error' => 'something went wrong'])),
            'anonymize' => $this->successResponse(json_encode(['text' => 'Hello'])),
        ]);
        $redactor = new PresidioRedactor($wpService, new PresidioRedactorConfig('https://presidio.example.com', 'https://presidio.example.com', 'en'));

        $this->expectException(PIIRedactionException::class);
        $redactor->extractAndRedactPII('Hello');
    }

    #[TestDox('extractAndRedactPII() throws a PIIRedactionException when the request body cannot be JSON-encoded (e.g. malformed UTF-8 input)')]
    public function testThrowsWhenRequestBodyCannotBeJsonEncoded(): void
    {
        $wpService = $this->getWpService([
            'analyze' => $this->successResponse('[]'),
            'anonymize' => $this->successResponse(json_encode(['text' => 'redacted'])),
        ]);
        $redactor = new PresidioRedactor($wpService, new PresidioRedactorConfig('https://presidio.example.com', 'https://presidio.example.com', 'en'));

        $this->expectException(PIIRedactionException::class);
        $redactor->extractAndRedactPII("\xB1\x31");
    }

    /**
     * @param array{analyze: array|\WP_Error, anonymize: array|\WP_Error} $responses
     *        Each value is either a fake response array (with 'status' and 'body' keys) or a WP_Error.
     */
    private function getWpService(array $responses): FakeWpService
    {
        return new FakeWpService([
            'wpRemotePost' => function (string $url) use ($responses) {
                return str_contains($url, '/analyze') ? $responses['analyze'] : $responses['anonymize'];
            },
            'isWpError' => fn(mixed $thing): bool => $thing instanceof \WP_Error,
            'wpRemoteRetrieveResponseCode' => fn(array|\WP_Error $response): int => $response['status'] ?? 200,
            'wpRemoteRetrieveBody' => fn(array|\WP_Error $response): string => $response['body'] ?? '',
        ]);
    }

    private function successResponse(string $body): array
    {
        return ['status' => 200, 'body' => $body];
    }

    private function findCallTo(FakeWpService $wpService, string $url): ?array
    {
        foreach ($wpService->methodCalls['wpRemotePost'] ?? [] as $call) {
            if ($call[0] === $url) {
                return $call;
            }
        }
        return null;
    }
}
