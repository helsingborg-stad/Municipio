<?php

namespace Municipio\PostObject;

use Municipio\PostObject\PostObjectInterface;
use Municipio\PostObject\PostObjectRenderer\PostObjectRendererInterface;
use WP_Post;
use WpService\Contracts\GetPermalink;

/**
 * PostObject from WP_Post.
 */
class PostObjectFromWpPost implements PostObjectInterface
{
    /**
     * Constructor.
     */
    public function __construct(private WP_Post $wpPost, private GetPermalink $wpService)
    {
    }

    /**
     * @inheritDoc
     */
    public function getId(): int
    {
        return $this->wpPost->ID;
    }

    /**
     * @inheritDoc
     */
    public function getTitle(): string
    {
        return $this->wpPost->post_title;
    }

    /**
     * @inheritDoc
     */
    public function getPermalink(): string
    {
        return $this->wpService->getPermalink($this->wpPost);
    }

    /**
     * @inheritDoc
     */
    public function getRendered(PostObjectRendererInterface $renderer): string
    {
        return $renderer->render($this);
    }
}
