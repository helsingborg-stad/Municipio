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
            return $this->innerConfig->getDateFrom();
        }

        $dateFrom = $this->innerConfig->getDateFrom();
        $today    = date('Y-m-d');

        if ($dateFrom === null || trim($dateFrom) === '') {
            return $today;
        }

        // A user-selected past date must not reintroduce expired events. A
        // future date remains a valid way to narrow the event archive.
        return max($today, $dateFrom);
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
