<?php

namespace Municipio\SchemaData\ApplySchemaDataToSearchIndexRecord;

use Override;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Contracts\AddFilter;
use WpService\Contracts\GetPostMeta;

class ApplySchemaDataToSearchIndexRecordTest extends TestCase {
    
    #[TestDox('can be instantiated')]
    public function testCanBeInstantiated(): void {
        $applySchemaDataToSearchIndexRecord = new ApplySchemaDataToSearchIndexRecord(static::getWpService());
        $this->assertInstanceOf(ApplySchemaDataToSearchIndexRecord::class, $applySchemaDataToSearchIndexRecord);
    }

    #[TestDox('attaches to the search index filter')]
    public function testAttachesToTheSearchIndexFilter(): void {
        $wpService = static::getWpService();
        $applySchemaDataToSearchIndexRecord = new ApplySchemaDataToSearchIndexRecord($wpService);
        
        $applySchemaDataToSearchIndexRecord->addHooks();

        static::assertCount(1, $wpService->filters);
        static::assertSame('Municipio/SearchIndex/Record', $wpService->filters[0]['hookName']);
    }

    #[TestDox('returns the supplied record')]
    public function testReturnsTheSuppliedRecord(): void {
        $wpService = static::getWpService();
        $applySchemaDataToSearchIndexRecord = new ApplySchemaDataToSearchIndexRecord($wpService);
        $record = ['foo' => 'bar'];

        $result = $applySchemaDataToSearchIndexRecord->applySchemaDataToSearchIndexRecord($record, 123);

        static::assertSame($record, $result);
    }

    #[TestDox('appends schema data to the search index record if available on the post')]
    public function testAppendsSchemaDataToTheSearchIndexRecordIfAvailableOnThePost(): void {
        $wpService = static::getWpService([
            123 => [
                'schemaData' => ['@type' => 'Article', 'headline' => 'Test Article']
            ]
        ]);
        $applySchemaDataToSearchIndexRecord = new ApplySchemaDataToSearchIndexRecord($wpService);
        $record = ['foo' => 'bar'];
        $postId = 123;

        $result = $applySchemaDataToSearchIndexRecord->applySchemaDataToSearchIndexRecord($record, $postId);

        static::assertArrayHasKey('schema_data', $result);
        static::assertSame(['@type' => 'Article', 'headline' => 'Test Article'], $result['schema_data']);
    }

    private static function getWpService(array $meta = []): AddFilter|GetPostMeta {
        return new class($meta) implements AddFilter, GetPostMeta {
            public array $filters = [];

            public function __construct(private array $meta)
            {
            }

            public function addFilter(string $hookName, callable $callback, int $priority = 10, int $acceptedArgs = 1): true
            {
                $this->filters[] = [
                    'hookName' => $hookName,
                    'callback' => $callback,
                    'priority' => $priority,
                    'acceptedArgs' => $acceptedArgs,
                ];
                return true;
            }

            public function getPostMeta(int $postId, string $key = '', bool $single = false): mixed
            {
                return $this->meta[$postId][$key] ?? null;
            }
        };
    }
}