<?php

declare(strict_types=1);


namespace Municipio\SchemaData\ApplySchemaDataToSearchIndexRecord;

use Municipio\PostObject\Factory\PostObjectFromWpPostFactoryInterface;
use Municipio\PostObject\NullPostObject;
use Municipio\PostObject\PostObjectInterface;
use Municipio\Schema\BaseType;
use Municipio\Schema\Schema;
use Municipio\Schema\Thing;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WP_Post;
use WpService\Contracts\AddFilter;
use WpService\Contracts\GetPost;

class ApplySchemaDataToSearchIndexRecordTest extends TestCase
{
    #[TestDox('can be instantiated')]
    public function testCanBeInstantiated(): void
    {
        $applySchemaDataToSearchIndexRecord = new ApplySchemaDataToSearchIndexRecord(static::getWpService(), static::createPostObjectFactory());
        static::assertInstanceOf(ApplySchemaDataToSearchIndexRecord::class, $applySchemaDataToSearchIndexRecord);
    }

    #[TestDox('attaches to the search index filter')]
    public function testAttachesToTheSearchIndexFilter(): void
    {
        $wpService = static::getWpService();
        $applySchemaDataToSearchIndexRecord = new ApplySchemaDataToSearchIndexRecord($wpService, static::createPostObjectFactory());

        $applySchemaDataToSearchIndexRecord->addHooks();

        static::assertCount(1, $wpService->filters);
        static::assertSame('Municipio/SearchIndex/Record', $wpService->filters[0]['hookName']);
    }

    #[TestDox('appends schema data to the search index record if available on the post')]
    public function testAppendsSchemaDataToTheSearchIndexRecordIfAvailableOnThePost(): void
    {
        $wpService = static::getWpService();
        $schema = Schema::event();
        $applySchemaDataToSearchIndexRecord = new ApplySchemaDataToSearchIndexRecord($wpService, static::createPostObjectFactory($schema));

        $result = $applySchemaDataToSearchIndexRecord->apply([], 123);

        static::assertArrayHasKey('schemaEvent', $result);
        static::assertSame('Event', $result['schemaEvent']['@type']);
    }

    #[TestDox('only applies schema data for supported schema types')]
    public function testAppliesForSupportedTypes(): void
    {
        $wpService = static::getWpService();
        $unsupportedSchema = Schema::adultEntertainment();
        $applySchemaDataToSearchIndexRecord = new ApplySchemaDataToSearchIndexRecord($wpService, static::createPostObjectFactory($unsupportedSchema));

        $result = $applySchemaDataToSearchIndexRecord->apply([], 123);

        static::assertArrayNotHasKey('AdultEntertainment', $result);
    }

    private static function getWpService(): AddFilter|GetPost
    {
        return new class implements AddFilter, GetPost {
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

            public function getPost(int|WP_Post|null $post = null, string $output = OBJECT, string $filter = 'raw'): WP_Post|array|null
            {
                return new WP_Post([]);
            }
        };
    }

    private static function createPostObjectFactory(BaseType $schema = new Thing()): PostObjectFromWpPostFactoryInterface
    {
        return new class($schema) implements PostObjectFromWpPostFactoryInterface {
            public function __construct(
                private BaseType $schema,
            ) {}

            public function create(WP_Post $post): PostObjectInterface
            {
                return new class($this->schema) extends NullPostObject {
                    public function __construct(
                        private BaseType $schema,
                    ) {}

                    public function getSchema(): BaseType
                    {
                        return $this->schema;
                    }
                };
            }
        };
    }
}
