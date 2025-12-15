<?php

declare(strict_types=1);

namespace Municipio\PostsList\Config\GetPostsConfig;

use Municipio\SchemaData\Utils\SchemaToPostTypesResolver\SchemaToPostTypeResolverInterface;

class GetPostsConfigWithPassedSchemaEventsFilteredOut extends AbstractDecoratedGetPostsConfig implements GetPostsConfigInterface
{
    public function __construct(
        protected GetPostsConfigInterface $innerConfig,
        private SchemaToPostTypeResolverInterface $resolvePostTypesFromSchemaType,
    ) {}

    public function getDateFrom(): null|string
    {
        if (!$this->currentPostTypesUseSchemaEvents()) {
            return null;
        }

        if ($this->innerConfig->getDateFrom() !== null && trim($this->innerConfig->getDateFrom()) !== '') {
            return $this->innerConfig->getDateFrom();
        }

        return date('Y-m-d');
    }

    private function currentPostTypesUseSchemaEvents(): bool
    {
        static $eventSchemaPostTypes = null;

        if ($eventSchemaPostTypes === null) {
            $eventSchemaPostTypes = $this->resolvePostTypesFromSchemaType->resolve('Event');
        }

        return count(array_intersect($this->innerConfig->getPostTypes(), $eventSchemaPostTypes)) > 0;
    }
}
