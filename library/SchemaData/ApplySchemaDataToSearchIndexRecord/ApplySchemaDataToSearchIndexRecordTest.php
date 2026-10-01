<?php

declare(strict_types=1);

namespace Municipio\SchemaData\ApplySchemaDataToSearchIndexRecord;

use Municipio\Schema\BaseType;
use Municipio\Schema\Schema;
use Municipio\Schema\Thing;
use Municipio\SchemaData\ApplySchemaDataToSearchIndexRecord\SchemaFromPostId\SchemaFromPostIdInterface;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Contracts\AddFilter;

class ApplySchemaDataToSearchIndexRecordTest extends TestCase
{
    #[TestDox('can be instantiated')]
    public function testCanBeInstantiated(): void
    {
        $applySchemaDataToSearchIndexRecord = new ApplySchemaDataToSearchIndexRecord(static::getWpService(), static::createSchemaFromPostId());
        static::assertInstanceOf(ApplySchemaDataToSearchIndexRecord::class, $applySchemaDataToSearchIndexRecord);
    }

    #[TestDox('attaches to the search index filter')]
    public function testAttachesToTheSearchIndexFilter(): void
    {
        $wpService = static::getWpService();
        $applySchemaDataToSearchIndexRecord = new ApplySchemaDataToSearchIndexRecord($wpService, static::createSchemaFromPostId());

        $applySchemaDataToSearchIndexRecord->addHooks();

        static::assertCount(1, $wpService->filters);
        static::assertSame('Municipio/SearchIndex/Record', $wpService->filters[0]['hookName']);
    }

    #[TestDox('appends schema data to the search index record if available on the post')]
    public function testAppendsSchemaDataToTheSearchIndexRecordIfAvailableOnThePost(): void
    {
        $wpService = static::getWpService();
        $schema = Schema::event();
        $applySchemaDataToSearchIndexRecord = new ApplySchemaDataToSearchIndexRecord($wpService, static::createSchemaFromPostId($schema));

        $result = $applySchemaDataToSearchIndexRecord->apply([], 123);

        static::assertArrayHasKey('schemaEvent', $result);
        static::assertSame('Event', $result['schemaEvent']['@type']);
    }

    #[TestDox('only applies schema data for supported schema types')]
    public function testAppliesForSupportedTypes(): void
    {
        $wpService = static::getWpService();
        $unsupportedSchema = Schema::adultEntertainment();
        $applySchemaDataToSearchIndexRecord = new ApplySchemaDataToSearchIndexRecord($wpService, static::createSchemaFromPostId($unsupportedSchema));

        $result = $applySchemaDataToSearchIndexRecord->apply([], 123);

        static::assertArrayNotHasKey('AdultEntertainment', $result);
    }

    private static function getWpService(): AddFilter
    {
        return new class implements AddFilter {
            public array $filters = [];

            public function __construct() {}

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
        };
    }

    private static function createSchemaFromPostId(BaseType $schema = new Thing()): SchemaFromPostIdInterface
    {
        return new class($schema) implements SchemaFromPostIdInterface {
            public function __construct(
                private BaseType $schema,
            ) {}

            public function getSchema(int $postId): BaseType
            {
                return $this->schema;
            }
        };
    }
}
