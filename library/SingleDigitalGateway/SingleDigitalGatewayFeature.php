<?php

declare(strict_types=1);

namespace Municipio\SingleDigitalGateway;

use Municipio\Schema\BaseType;
use WpService\Contracts\AddAction;

class SingleDigitalGatewayFeature
{
    public function __construct(
        private AddAction $wpService,
    ) {}

    public function enable(): void
    {
        $this->wpService->addAction('Municipio\SchemaData\OutputPostSchemaJsonInSingleHead\Print', [
            $this,
            'printMetaTags',
        ]);
    }

    public function printMetaTags(BaseType $schema): void
    {
        if ($schema->getType() !== 'SingleDigitalGateway') {
            return;
        }

        foreach ((new GetMetaTagsFromSchema($schema))->getMetaTags() as $metaTag) {
            echo PHP_EOL . $metaTag;
        }
    }
}
