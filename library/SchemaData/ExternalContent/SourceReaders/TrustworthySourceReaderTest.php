<?php

declare(strict_types=1);


namespace Municipio\SchemaData\ExternalContent\SourceReaders;

use Override;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Contracts\__;

class TrustworthySourceReaderTest extends TestCase {
    #[TestDox('returns result from inner if the inner source produces the same result on allowed number of subsequent calls')]
    public function testReturnsResultFromInnerIfTheInnerSourceProducesTheSameResultOnAllowedNumberOfSubsequentCalls(): void
    {
        $maxNumberOfCalls = 3;
        $responses = [
            [
                ['document' => ['id' => 1, 'name' => 'test']]
            ],
            [
                ['document' => ['id' => 1, 'name' => 'test']],
                ['document' => ['id' => 2, 'name' => 'test2']]
            ],
            [
                ['document' => ['id' => 1, 'name' => 'test']],
                ['document' => ['id' => 2, 'name' => 'test2']]
            ]
        ];
        $innerSourceReader = static::createInnerSourceReader($responses);

        $trustworthySourceReader = new TrustworthySourceReader($innerSourceReader, static::createWpService(), $maxNumberOfCalls, 0);

        $result = $trustworthySourceReader->getSourceData();

        static::assertSame([
                ['document' => ['id' => 1, 'name' => 'test']],
                ['document' => ['id' => 2, 'name' => 'test2']]
        ], $result);
    }

    #[TestDox('throws an exception if the inner source produces different results on allowed number of subsequent calls')]
    public function testThrowsAnExceptionIfTheInnerSourceProducesDifferentResultsOnAllowedNumberOfSubsequentCalls(): void
    {
        $maxNumberOfCalls = 5;
        $responses = [];
        
        for ($i = 0; $i < $maxNumberOfCalls+1; $i++) {
            $responses[] = [
                ['document' => ['id' => $i, 'name' => "test{$i}"]], // Different response on each call.
            ];
        }

        $innerSourceReader = static::createInnerSourceReader($responses);
        $trustworthySourceReader = new TrustworthySourceReader($innerSourceReader, static::createWpService(), $maxNumberOfCalls, 0, static::createNullLogger());

        try{
            $trustworthySourceReader->getSourceData();
        } catch (\RuntimeException $e) {
            static::assertInstanceOf(\RuntimeException::class, $e);
            return;
        }

        static::fail('Expected exception was not thrown');
    }

    private static function createNullLogger(): Logger\LoggerInterface {
        return new class implements Logger\LoggerInterface {
            #[Override]
            public function logError(string $message): void
            {
            }
        };
    }

    private static function createInnerSourceReader(array $responses = []):SourceReaderInterface {
        return new class($responses) implements SourceReaderInterface {
            private int $callCount = 0;

            public function __construct(private array $responses)
            {
            }

            public function getSourceData(): array
            {
                $this->callCount++;
                return $this->responses[$this->callCount - 1] ?? [];
            }
        };
    }

    private static function createWpService(): __ {
        return new class implements __ {
            public function __(string $text, string $domain = 'default'): string
            {
                return $text;
            }
        };
    }
}