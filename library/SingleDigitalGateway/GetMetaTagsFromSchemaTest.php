<?php

declare(strict_types=1);

namespace Municipio\SingleDigitalGateway;

use Municipio\Schema\Schema;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class GetMetaTagsFromSchemaTest extends TestCase
{
    #[TestDox('returns correct meta tags from SingleDigitalGateway schema')]
    public function testGetMetaTagsFromSchema(): void
    {
        $schema = Schema::singleDigitalGateway()
            ->setProperty('policyCode', '12345')
            ->setProperty('service', ['information', 'procedure'])
            ->setProperty('policy', 'Sample Policy')
            ->setProperty('location', 'Sample Location');

        $getMetaTags = new GetMetaTagsFromSchema($schema);
        $metaTags = $getMetaTags->getMetaTags();

        static::assertSame('<meta name="policy-code" content="12345">', (string) $metaTags[0]);
        static::assertSame('<meta name="DC.Service" content="information;procedure">', (string) $metaTags[1]);
        static::assertSame('<meta name="DC.Policy" content="Sample Policy">', (string) $metaTags[2]);
        static::assertSame('<meta name="DC.Location" content="Sample Location">', (string) $metaTags[3]);
    }

    #[TestDox('handles service as string')]
    public function testHandlesServiceAsString(): void
    {
        $schema = Schema::singleDigitalGateway()->setProperty('service', 'information');

        $getMetaTags = new GetMetaTagsFromSchema($schema);
        $metaTags = $getMetaTags->getMetaTags();

        static::assertSame('<meta name="DC.Service" content="information">', (string) $metaTags[0]);
    }

    #[TestDox('returns static meta tags when other properties are present')]
    public function testReturnsStaticMetaTagsWhenOtherPropertiesArePresent(): void
    {
        $schema = Schema::singleDigitalGateway()->setProperty('policyCode', '12345');

        $getMetaTags = new GetMetaTagsFromSchema($schema);
        $metaTags = $getMetaTags->getMetaTags();

        static::assertSame('<meta name="sdg-tag" content="sdg">', (string) $metaTags[1]);
        static::assertSame('<meta name="DC.ISO3166" content="SE">', (string) $metaTags[2]);
        static::assertSame('<meta name="DC.Language" content="en">', (string) $metaTags[3]);
    }
}
