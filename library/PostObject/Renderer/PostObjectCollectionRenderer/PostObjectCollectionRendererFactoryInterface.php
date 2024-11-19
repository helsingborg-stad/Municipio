<?php

namespace Municipio\PostObject\Renderer\PostObjectCollectionRenderer;

interface PostObjectCollectionRendererFactoryInterface
{
    /**
     * @inheritDoc
     */
    public function create(PostObjectCollectionRendererType $type): PostObjectCollectionRendererInterface;
}
