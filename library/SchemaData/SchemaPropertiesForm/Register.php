<?php

namespace Municipio\SchemaData\SchemaPropertiesForm;

use AcfService\Contracts\AddLocalFieldGroup;
use Municipio\Config\Features\SchemaData\Contracts\TryGetSchemaTypeFromPostType;
use Municipio\HooksRegistrar\Hookable;
use Municipio\PostObject\Factory\PostObjectFromWpPostFactoryInterface;
use Municipio\Schema\BaseType;
use Municipio\Schema\Schema;
use Municipio\SchemaData\SchemaPropertiesForm\FormBuilder\FormFactory\FormFactoryInterface;
use WP_Post;
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
        private PostObjectFromWpPostFactoryInterface $postObjectFactory,
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

    /**
     * Get the schema object.
     *
     * @return BaseType
     */
    private function getSchema(): BaseType
    {
        if ($postId = $this->getPostIdFromRequest()) {
            $post = $this->wpService->getPost($postId);

            if (is_a($post, WP_Post::class)) {
                return $this->postObjectFactory->create($post)->getSchema();
            }
        }

        return Schema::{strtolower($this->getSchemaType())}();
    }

    /**
     * Get the post ID from the request.
     *
     * @return int
     */
    private function getPostIdFromRequest(): int
    {
        if (isset($_GET['post']) && is_numeric($_GET['post'])) {
            return (int)$_GET['post'];
        }

        // phpcs:ignore WordPress.Security.NonceVerification.Missing
        if (isset($_POST['post_ID']) && is_numeric($_POST['post_ID'])) {
            // phpcs:ignore WordPress.Security.NonceVerification.Missing
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

        return !empty($screen) && $screen->base === 'post' && !empty($screen->post_type);
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
