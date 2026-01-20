<?php

namespace Municipio\SchemaData\ExternalContent\WpPostArgsFromSchemaObject\Factory;

use Municipio\Helper\WpService;
use Municipio\SchemaData\ExternalContent\Config\SourceConfigInterface;
use Municipio\SchemaData\ExternalContent\WpPostArgsFromSchemaObject\DateDecorator;
use Municipio\SchemaData\ExternalContent\WpPostArgsFromSchemaObject\IdDecorator;
use Municipio\SchemaData\ExternalContent\WpPostArgsFromSchemaObject\JobPostingDecorator;
use Municipio\SchemaData\ExternalContent\WpPostArgsFromSchemaObject\MetaPropertyValueDecorator;
use Municipio\SchemaData\ExternalContent\WpPostArgsFromSchemaObject\OriginIdDecorator;
use Municipio\SchemaData\ExternalContent\WpPostArgsFromSchemaObject\PostContentDecorator;
use Municipio\SchemaData\ExternalContent\WpPostArgsFromSchemaObject\PostTypeDecorator;
use Municipio\SchemaData\ExternalContent\WpPostArgsFromSchemaObject\SchemaDataDecorator;
use Municipio\SchemaData\ExternalContent\WpPostArgsFromSchemaObject\SourceIdDecorator;
use Municipio\SchemaData\ExternalContent\WpPostArgsFromSchemaObject\ThumbnailDecorator;
use Municipio\SchemaData\ExternalContent\WpPostArgsFromSchemaObject\WpPostArgsFromSchemaObject;
use Municipio\SchemaData\ExternalContent\WpPostArgsFromSchemaObject\WpPostArgsFromSchemaObjectInterface;

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
    public function __construct(
        private SourceConfigInterface $sourceConfig,
    ) {}

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

        return $postArgsFromSchemaObject;
    }
}
