<?php

namespace Municipio\PostObject\Decorators;

use Municipio\PostObject\Icon\IconInterface;
use Municipio\PostObject\PostObjectInterface;
use WpService\Contracts\GetCurrentBlogId;
use WpService\Contracts\MsIsSwitched;

/**
 * PostObjectWithOtherBlogIdFromSwitchedState
 */
class PostObjectWithOtherBlogIdFromSwitchedState implements PostObjectInterface
{
    private ?int $blogId = null;

    /**
     * Constructor.
     */
    public function __construct(
        private PostObjectInterface $postObject,
        private MsIsSwitched&GetCurrentBlogId $wpService
    ) {
        if ($this->wpService->msIsSwitched() === true) {
            $this->blogId = $this->wpService->getCurrentBlogId();
        }
    }

    /**
     * @inheritDoc
     */
    public function getId(): int
    {
        return $this->postObject->getId();
    }

    /**
     * @inheritDoc
     */
    public function getTitle(): string
    {
        return $this->postObject->getTitle();
    }

    /**
     * @inheritDoc
     */
    public function getPermalink(): string
    {
        return $this->postObject->getPermalink();
    }

    /**
     * @inheritDoc
     */
    public function getCommentCount(): int
    {
        return $this->postObject->getCommentCount();
    }

    /**
     * @inheritDoc
     */
    public function getPostType(): string
    {
        return $this->postObject->getPostType();
    }

    /**
     * @inheritDoc
     */
    public function getIcon(): ?IconInterface
    {
        return $this->postObject->getIcon();
    }

    /**
     * @inheritDoc
     */
    public function getBlogId(): int
    {
        return $this->blogId ?? $this->postObject->getBlogId();
    }
}
