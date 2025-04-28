<?php

namespace Municipio\SchemaData\SchemaPropertiesForm;

use AcfService\Contracts\AddLocalFieldGroup;
use Municipio\Config\Features\SchemaData\Contracts\TryGetSchemaTypeFromPostType;
use Municipio\HooksRegistrar\Hookable;
use WpService\Contracts\AddAction;
use WpService\Contracts\GetCurrentScreen;
use WpService\Contracts\GetPostMeta;

/**
 * Class Register
 */
class Register implements Hookable
{
    /**
     * Constructor.
     */
    public function __construct(
        private AddLocalFieldGroup $acfService,
        private AddAction&GetCurrentScreen&GetPostMeta $wpService,
        private GetAcfFieldGroupBySchemaTypeInterface $getAcfFieldGroupBySchemaType,
        private TryGetSchemaTypeFromPostType $configService,
    ) {
    }

    /**
     * @inheritDoc
     */
    public function addHooks(): void
    {
        $this->wpService->addAction('current_screen', [$this, 'register']);
    }

    /**
     * Register the form.
     *
     * @return void
     */
    public function register(): void
    {
        if (!$this->shouldRegisterForm()) {
            return;
        }

        $this->acfService->addLocalFieldGroup($this->getAcfFieldGroupBySchemaType->getAcfFieldGroup($this->getSchemaType()));
    }

    /**
     * Check if the form should be registered.
     *
     * @return bool
     */
    private function shouldRegisterForm(): bool
    {
        return $this->isOnEditPostScreen() && !$this->currentPostIsFromExternalSource() && !empty($this->getSchemaType());
    }

    /**
     * Check if the current screen is the edit post screen.
     *
     * @return bool
     */
    private function isOnEditPostScreen(): bool
    {
        $screen = $this->wpService->getCurrentScreen();

        return !empty($screen) && $screen->base === 'post' && isset($screen->post_type);
    }

    /**
     * Check if the current post is from an external source.
     *
     * @return bool
     */
    private function currentPostIsFromExternalSource(): bool
    {
        if (isset($_GET['post']) && is_numeric($_GET['post'])) {
            return !empty($this->wpService->getPostMeta($_GET['post'], 'originId', true));
        }

        return false;
    }

    /**
     * Get the schema type.
     *
     * @return string|null
     */
    private function getSchemaType(): ?string
    {
        return $this->configService->tryGetSchemaTypeFromPostType($this->wpService->getCurrentScreen()->post_type);
    }
}
