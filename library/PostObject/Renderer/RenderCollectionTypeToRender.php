<?php

namespace Municipio\PostObject\Renderer;

use UnitEnum;

/**
 * Class RenderItemTypeToRender
 */
class RenderCollectionTypeToRender implements RenderTypeToRenderInterface
{
    /**
     * RenderItemTypeToRender constructor.
     */
    public function __construct(
        private RenderDirectorInterface $renderFactory,
        private array $config = []
    ) {
    }

    /**
     * @inheritDoc
     */
    public function getRenderFromRenderType(UnitEnum $type): RenderInterface
    {
        $method = $this->getRenderMethod($type);

        if ($method === null) {
            throw new \InvalidArgumentException("Unknown render type: " . $type->value);
        }

        return $this->renderFactory->{$method}($this->config);
    }

    /**
     * Get render method.
     */
    private function getRenderMethod(UnitEnum $type): ?string
    {
        return match ($type) {
            RenderCollectionType::SegmentGridItemCollection => 'getSegmentGridItemCollectionRender',
            RenderCollectionType::SchemaProjectItemCollection => 'getSchemaProjectItemCollectionRender',
            RenderCollectionType::NewsItemCollection => 'getNewsItemCollectionRender',
            RenderCollectionType::CompressedItemCollection => 'getCompressedItemCollectionRender',
            RenderCollectionType::CollectionItemCollection => 'getCollectionItemCollectionRender',
            RenderCollectionType::CardItemCollection => 'getCardItemCollectionRender',
            RenderCollectionType::ListItemCollection => 'getListItemCollectionRender',
            RenderCollectionType::BoxGridItemCollection => 'getBoxGridItemCollectionRender',
            RenderCollectionType::BlockItemCollection => 'getBlockItemCollectionRender',
            default => null,
        };
    }
}
