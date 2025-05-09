<?php

namespace Municipio\SchemaData\SchemaPropertiesForm;

use AcfService\Contracts\AddLocalFieldGroup;
use Municipio\Config\Features\SchemaData\Contracts\TryGetSchemaTypeFromPostType;
use Municipio\Helper\Post;
use Municipio\HooksRegistrar\Hookable;
use Municipio\Schema\BaseType;
use Municipio\Schema\Schema;
use Municipio\SchemaData\SchemaPropertiesForm\FormBuilder\FormFactory\FormFactoryInterface;
use WpService\Contracts\AddAction;
use WpService\Contracts\GetCurrentScreen;
use WpService\Contracts\GetPost;
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
        private AddAction&GetCurrentScreen&GetPostMeta&GetPost $wpService,
        private TryGetSchemaTypeFromPostType $configService,
        private FormFactoryInterface $formFactory,
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

        $this->acfService->addLocalFieldGroup($this->formFactory->createForm($this->getSchema()));
    }

    private function getSchema(): BaseType
    {
        if ($postId = $this->getPostIdFromRequest()) {
            $post = $this->wpService->getPost($postId);
            return Post::preparePostObject($post)->getSchema();
        }

        return Schema::{strtolower($this->getSchemaType())}();
    }

    private function getPostIdFromRequest(): int
    {
        if (isset($_GET['post']) && is_numeric($_GET['post'])) {
            return (int)$_GET['post'];
        }

        if (isset($_POST['post_ID']) && is_numeric($_POST['post_ID'])) {
            return (int)$_POST['post_ID'];
        }

        return 0;
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
