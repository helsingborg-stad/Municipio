<?php

declare(strict_types=1);

namespace Municipio\SchemaData\ExternalContent\SyncHandler\MetaDataFromSchema;

use Municipio\Schema\Schema;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class MetaDataItemsFromSchemaTest extends TestCase
{
    #[TestDox('Takes schema object and returns array')]
    public function testMetaDataItemsFromSchema(): void
    {
        $metaDataFromSchema = new MetaDataItemsFromSchema();
        static::assertIsArray($metaDataFromSchema->getMetaDataItems(Schema::thing()));
    }
}
