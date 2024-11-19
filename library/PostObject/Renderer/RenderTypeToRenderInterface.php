<?php

namespace Municipio\PostObject\Renderer;

use UnitEnum;

interface RenderTypeToRenderInterface
{
    /**
     * Get Render from render type
     *
     * @param UnitEnum $type
     * @return RenderInterface
     */
    public function getRenderFromRenderType(UnitEnum $type): RenderInterface;
}
