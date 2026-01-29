<?php

namespace Municipio\SchemaData\SchemaPropertiesForm\SetPostTitleFromSchemaTitle;

use Municipio\HooksRegistrar\Hookable;
use Municipio\SchemaData\Helper\GetSchemaType;
use Municipio\SchemaData\SchemaObjectFromPost\SchemaObjectFromPostInterface;
use WpService\Contracts\AddAction;
use WpService\Contracts\WpUpdatePost;

/**
 * Class SetPostTitleFromSchemaTitle
 *
 * This class listens to the 'save_post' action and updates the post title
 * based on the 'name' property from the schema object associated with the post.
 */
class SetPostTitleFromSchemaTitle implements Hookable
{
    /**
     * SetPostTitleFromSchemaTitle constructor.
     *
     * @param SchemaObjectFromPostInterface $schemaObjectFromPost
     * @param WpUpdatePost&AddAction $wpService
     */
    public function __construct(
        private SchemaObjectFromPostInterface $schemaObjectFromPost,
        private WpUpdatePost&AddAction $wpService,
    ) {}

    /**
     * @inheritDoc
     */
    public function addHooks(): void
    {
        $this->wpService->addAction('save_post', [$this, 'setPostTitleFromSchemaTitle'], 10, 2);
    }

    /**
     * Sets the post title from the schema title if it is different.
     *
     * @param int $postId The ID of the post being saved.
     * @param \WP_Post $post The post object being saved.
     */
    public function setPostTitleFromSchemaTitle(int $postId, \WP_Post $post): void
    {
        if (empty(GetSchemaType::getSchemaTypeFromPostType($post->post_type))) {
            return;
        }

        $schemaObject = $this->schemaObjectFromPost->create($post);
        $schemaTitle = $schemaObject->getProperty('name');

        if ($this->shouldUpdateTitle($schemaTitle, $post->post_title)) {
            $this->updatePostTitle($postId, $schemaTitle);
        }
    }

    /**
     * Determines if the post title should be updated based on the schema title.
     *
     * @param string|null $schemaTitle The title from the schema.
     * @param string $currentTitle The current post title.
     * @return bool True if the title should be updated, false otherwise.
     */
    private function shouldUpdateTitle(?string $schemaTitle, string $currentTitle): bool
    {
        return !empty($schemaTitle) && $schemaTitle !== $currentTitle;
    }

    /**
     * Updates the post title in WordPress.
     *
     * @param int $postId The ID of the post to update.
     * @param string $newTitle The new title to set for the post.
     */
    private function updatePostTitle(int $postId, string $newTitle): void
    {
        $this->wpService->wpUpdatePost([
            'ID' => $postId,
            'post_title' => $newTitle,
        ]);
    }
}
