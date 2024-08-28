<?php

namespace Municipio\SchemaData\SchemaPropertiesForm;

use AcfService\Contracts\AddLocalFieldGroup;
use Municipio\Config\Features\SchemaData\Contracts\TryGetSchemaTypeFromPostType;
use Municipio\HooksRegistrar\Hookable;
use WpService\Contracts\AddAction;
use WpService\Contracts\GetCurrentScreen;

class Register implements Hookable
{
    public function __construct(
        private AddLocalFieldGroup $acfService,
        private AddAction&GetCurrentScreen $wpService,
        private GetAcfFieldGroupBySchemaTypeInterface $getAcfFieldGroupBySchemaType,
        private TryGetSchemaTypeFromPostType $configService,
    ) {
    }

    public function addHooks(): void
    {
        $this->wpService->addAction('current_screen', [$this, 'register']);
    }

    public function register(): void
    {
        $screen = $this->wpService->getCurrentScreen();

        if (empty($screen) || !isset($screen->post_type) || $screen->base !== 'post') {
            return;
        }

        $schemaType = $this->configService->tryGetSchemaTypeFromPostType($screen->post_type);

        if (empty($schemaType)) {
            return;
        }

        $this->acfService->addLocalFieldGroup($this->getAcfFieldGroupBySchemaType->getAcfFieldGroup($schemaType));
    }
}
