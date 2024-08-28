<?php

namespace Municipio\ExternalContent\Config;

use Municipio\Config\Features\SchemaData\Contracts\TryGetSchemaTypeFromPostType;
use Municipio\HooksRegistrar\Hookable;
use WpService\Contracts\GetPostTypes;

class SourceConfigRegistry implements SourceConfigRegistryInterface, Hookable
{
    private array $sourceConfigurations = [];

    public function __construct(
        private GetPostTypes $wpService,
        private TryGetSchemaTypeFromPostType $tryGetSchemaTypeFromPostType,
        private ConfigFactoryInterface $configFactoryInterface
    ) {
    }

    public function addHooks(): void
    {
        add_action('init', array($this, 'setupRegistry'));
    }

    public function setupRegistry(): void
    {
        $postTypes = $this->wpService->getPostTypes();

        foreach ($postTypes as $postType) {
            $schemaType = $this->tryGetSchemaTypeFromPostType->tryGetSchemaTypeFromPostType($postType);

            if ($schemaType === null) {
                continue;
            }

            $this->sourceConfigurations[] = $this->configFactoryInterface->create($postType);
        }
    }

    public function getSourceConfigurations(): array
    {
        return $this->sourceConfigurations;
    }
}
