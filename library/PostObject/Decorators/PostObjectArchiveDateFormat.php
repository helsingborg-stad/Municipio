<?php

namespace Municipio\PostObject\Decorators;

use Municipio\PostObject\Date\ArchiveDateFormatResolverInterface;
use Municipio\PostObject\Icon\IconInterface;
use Municipio\PostObject\PostObjectInterface;

/**
 * PostObjectWithSeoRedirect class.
 *
 * Applies the SEO redirect to the post object permalink if a redirect is set.
 */
class PostObjectArchiveDateFormat implements PostObjectInterface
{
    /**
     * Constructor.
     */
    public function __construct(
        private PostObjectInterface $postObject,
        private ArchiveDateFormatResolverInterface $archiveDateFormatSettingResolver
    ) {
    }

    /**
     * @inheritDoc
     * @codeCoverageIgnore
     */
    public function getId(): int
    {
        return $this->postObject->getId();
    }

    /**
     * @inheritDoc
     * @codeCoverageIgnore
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
     * @codeCoverageIgnore
     */
    public function getCommentCount(): int
    {
        return $this->postObject->getCommentCount();
    }

    /**
     * @inheritDoc
     * @codeCoverageIgnore
     */
    public function getPostType(): string
    {
        return $this->postObject->getPostType();
    }

    /**
     * @inheritDoc
     * @codeCoverageIgnore
     */
    public function getBlogId(): int
    {
        return $this->postObject->getBlogId();
    }

    /**
     * @inheritDoc
     * @codeCoverageIgnore
     */
    public function getIcon(): ?IconInterface
    {
        return $this->postObject->getIcon();
    }

    /**
     * @inheritDoc
     */
    public function getPublishedTime(bool $gmt = false): int
    {
        return $this->postObject->getPublishedTime($gmt);
    }

    /**
     * @inheritDoc
     */
    public function getModifiedTime(bool $gmt = false): int
    {
        return $this->postObject->getModifiedTime($gmt);
    }

    /**
     * @inheritDoc
     */
    public function getArchiveDateTimestamp(): int
    {
        return $this->postObject->getArchiveDateTimestamp();
    }

    /**
     * @inheritDoc
     */
    public function getArchiveDateFormat(): string
    {
        // echo '<pre>' . print_r( $this->archiveDateFormatSettingResolver->resolve(), true ) . '</pre>';
        return $this->archiveDateFormatSettingResolver->resolve();
    }
}
