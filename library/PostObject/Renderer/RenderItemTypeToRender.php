<?php

namespace Municipio\PostObject\Renderer;

use Municipio\PostObject\PostObjectInterface;
use UnitEnum;

/**
 * Class RenderItemTypeToRender
 */
class RenderItemTypeToRender implements RenderTypeToRenderInterface
{
    /**
     * RenderItemTypeToRender constructor.
     */
    public function __construct(
        private RenderDirectorInterface $renderFactory,
        private PostObjectInterface $postObject,
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
            throw new \InvalidArgumentException("Unknown render type: " . $type->value); // phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped
        }

        return $this->renderFactory->{$method}($this->postObject, $this->config);
    }

    /**
     * Get render method.
     */
    private function getRenderMethod(UnitEnum $type): ?string
    {
        return match ($type) {
            RenderItemType::BlockItem         => 'getBlockItemRender',
            RenderItemType::BoxGridItem       => 'getBoxGridItemRender',
            RenderItemType::BoxItem           => 'getBoxItemRender',
            RenderItemType::BoxSliderItem     => 'getBoxSliderItemRender',
            RenderItemType::CardItem          => 'getCardItemRender',
            RenderItemType::CollectionItem    => 'getCollectionItemRender',
            RenderItemType::CompressedItem    => 'getCompressedItemRender',
            RenderItemType::ListItem          => 'getListItemRender',
            RenderItemType::NewsItem          => 'getNewsItemRender',
            RenderItemType::SchemaProjectItem => 'getSchemaProjectItemRender',
            RenderItemType::SegmentGridItem   => 'getSegmentGridItemRender',
            RenderItemType::SegmentItem       => 'getSegmentItemRender',
            RenderItemType::SegmentSliderItem => 'getSegmentSliderItemRender',
            default                           => null,
        };
    }
}
