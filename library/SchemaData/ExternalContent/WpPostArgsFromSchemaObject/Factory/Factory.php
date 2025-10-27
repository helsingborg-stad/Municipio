<?php

namespace Municipio\SchemaData\ExternalContent\WpPostArgsFromSchemaObject\Factory;

use Municipio\SchemaData\ExternalContent\Config\SourceConfigInterface;
use Municipio\SchemaData\ExternalContent\WpPostArgsFromSchemaObject\{
    DateDecorator,
    EventDatesDecorator,
    IdDecorator,
    JobPostingDecorator,
    MetaPropertyValueDecorator,
    OriginIdDecorator,
    PostContentDecorator,
    PostTypeDecorator,
    SchemaDataDecorator,
    SourceIdDecorator,
    ThumbnailDecorator,
    WpPostArgsFromSchemaObject,
    WpPostArgsFromSchemaObjectInterface,
};
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
        $postArgsFromSchemaObject = new PostContentDecorator($postArgsFromSchemaObject);
        $postArgsFromSchemaObject = new PostTypeDecorator($this->sourceConfig->getPostType(), $postArgsFromSchemaObject);
        $postArgsFromSchemaObject = new DateDecorator($postArgsFromSchemaObject);
        $postArgsFromSchemaObject = new IdDecorator($this->sourceConfig->getPostType(), $this->sourceConfig->getId(), $postArgsFromSchemaObject, WpService::get());
        $postArgsFromSchemaObject = new JobPostingDecorator($postArgsFromSchemaObject);
        $postArgsFromSchemaObject = new SchemaDataDecorator($postArgsFromSchemaObject);
        $postArgsFromSchemaObject = new OriginIdDecorator($postArgsFromSchemaObject);
        $postArgsFromSchemaObject = new ThumbnailDecorator($postArgsFromSchemaObject);
        $postArgsFromSchemaObject = new SourceIdDecorator($this->sourceConfig->getId(), $postArgsFromSchemaObject);
        $postArgsFromSchemaObject = new MetaPropertyValueDecorator($postArgsFromSchemaObject);
        $postArgsFromSchemaObject = new EventDatesDecorator($postArgsFromSchemaObject);

        return $postArgsFromSchemaObject;
    }
}
