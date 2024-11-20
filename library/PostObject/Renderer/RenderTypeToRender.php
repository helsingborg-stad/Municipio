<?php

namespace Municipio\PostObject\Renderer;

use Municipio\PostObject\PostObjectInterface;
use UnitEnum;

/**
 * Class RenderItemTypeToRender
 */
class RenderTypeToRender implements RenderTypeToRenderInterface
{
    /**
     * RenderItemTypeToRender constructor.
     */
    public function __construct(
        private RenderDirectorInterface $renderFactory,
    ) {
    }

    /**
     * @inheritDoc
     */
    public function getRenderFromRenderType(RenderType $type, array $config = []): RenderInterface
    {
        $method = $this->getRenderMethod($type);

        if ($method === null) {
            throw new \InvalidArgumentException("Unknown render type: " . $type->value); // phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped
        }

        return $this->renderFactory->{$method}($config);
    }

    /**
     * Get render method.
     */
    private function getRenderMethod(UnitEnum $type): ?string
    {
        return match ($type) {
            // Item types
            RenderType::BlockItem         => 'getBlockItemRender',
            RenderType::BoxItem           => 'getBoxItemRender',
            RenderType::BoxSliderItem     => 'getBoxSliderItemRender',
            RenderType::CardItem          => 'getCardItemRender',
            RenderType::CollectionItem    => 'getCollectionItemRender',
            RenderType::CompressedItem    => 'getCompressedItemRender',
            RenderType::ListItem          => 'getListItemRender',
            RenderType::NewsItem          => 'getNewsItemRender',
            RenderType::SchemaProjectItem => 'getSchemaProjectItemRender',
            RenderType::SegmentGridItem   => 'getSegmentGridItemRender',
            RenderType::SegmentItem       => 'getSegmentItemRender',
            RenderType::SegmentSliderItem => 'getSegmentSliderItemRender',

            // Collection types
            RenderType::SegmentGridItemCollection => 'getSegmentGridItemCollectionRender',
            RenderType::SchemaProjectItemCollection => 'getSchemaProjectItemCollectionRender',
            RenderType::NewsItemCollection => 'getNewsItemCollectionRender',
            RenderType::CompressedItemCollection => 'getCompressedItemCollectionRender',
            RenderType::CollectionItemCollection => 'getCollectionItemCollectionRender',
            RenderType::CardItemCollection => 'getCardItemCollectionRender',
            RenderType::ListItemCollection => 'getListItemCollectionRender',
            RenderType::BoxGridItemCollection => 'getBoxGridItemCollectionRender',
            RenderType::BlockItemCollection => 'getBlockItemCollectionRender',
            default                           => null,
        };
    }
}
