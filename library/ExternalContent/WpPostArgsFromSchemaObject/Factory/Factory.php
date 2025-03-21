<?php

namespace Municipio\ExternalContent\WpPostArgsFromSchemaObject\Factory;

use Municipio\ExternalContent\Config\SourceConfigInterface;
use Municipio\ExternalContent\WpPostArgsFromSchemaObject\{
    AddChecksum,
    DateDecorator,
    EventDatesDecorator,
    IdDecorator,
    JobPostingDecorator,
    MetaPropertyValueDecorator,
    OriginIdDecorator,
    PostTypeDecorator,
    SchemaDataDecorator,
    SourceIdDecorator,
    TermsDecorator,
    ThumbnailDecorator,
    VerifyChecksum,
    WpPostArgsFromSchemaObject,
    WpPostArgsFromSchemaObjectInterface,
};
use Municipio\ExternalContent\WpTermFactory\WpTermFactoryInterface;
use Municipio\Helper\WpService;

/**
 * Factory for creating WpPostArgsFromSchemaObject instances.
 */
class Factory implements FactoryInterface
{
    /**
     * Class constructor
     *
     * @param SourceConfigInterface $sourceConfig
     */
    public function __construct(private SourceConfigInterface $sourceConfig)
    {
    }

    /**
     * @inheritDoc
     */
    public function create(): WpPostArgsFromSchemaObjectInterface
    {
        $postArgsFromSchemaObject = new WpPostArgsFromSchemaObject();
        $postArgsFromSchemaObject = new PostTypeDecorator($this->sourceConfig->getPostType(), $postArgsFromSchemaObject);
        $postArgsFromSchemaObject = new DateDecorator($postArgsFromSchemaObject);
        $postArgsFromSchemaObject = new IdDecorator($this->sourceConfig->getPostType(), $this->sourceConfig->getId(), $postArgsFromSchemaObject, WpService::get());
        $postArgsFromSchemaObject = new JobPostingDecorator($postArgsFromSchemaObject);
        $postArgsFromSchemaObject = new SchemaDataDecorator($postArgsFromSchemaObject);
        $postArgsFromSchemaObject = new OriginIdDecorator($postArgsFromSchemaObject);
        $postArgsFromSchemaObject = new ThumbnailDecorator($postArgsFromSchemaObject, WpService::get());
        $postArgsFromSchemaObject = new SourceIdDecorator($this->sourceConfig->getId(), $postArgsFromSchemaObject);
        $postArgsFromSchemaObject = new MetaPropertyValueDecorator($postArgsFromSchemaObject);
        $postArgsFromSchemaObject = new TermsDecorator($this->sourceConfig->getTaxonomies(), $this->getWpTermFactory(), WpService::get(), $postArgsFromSchemaObject); // phpcs:ignore Generic.Files.LineLength.TooLong
        $postArgsFromSchemaObject = new EventDatesDecorator($postArgsFromSchemaObject);

        $postArgsFromSchemaObject = new AddChecksum($postArgsFromSchemaObject);
        $postArgsFromSchemaObject = new VerifyChecksum($postArgsFromSchemaObject, WpService::get());

        return $postArgsFromSchemaObject;
    }

    /**
     * Retrieves an instance of WpTermFactoryInterface.
     *
     * @return WpTermFactoryInterface An instance of WpTermFactoryInterface.
     */
    private function getWpTermFactory(): WpTermFactoryInterface
    {
        $wpTermFactory = new \Municipio\ExternalContent\WpTermFactory\WpTermFactory();
        return new \Municipio\ExternalContent\WpTermFactory\WpTermUsingSchemaObjectName($wpTermFactory);
    }
}
