<?php

namespace Municipio\PostObject\Renderer;

use UnitEnum;

interface RenderTypeToRenderInterface
{
    /**
     * Get Render from render type
     *
     * @param UnitEnum $type
     * @param array $config
     * @return RenderInterface
     */
    public function getRenderFromRenderType(RenderType $type, array $config = []): RenderInterface;
}
