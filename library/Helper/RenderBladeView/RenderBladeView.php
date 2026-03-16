<?php

namespace Municipio\Helper\RenderBladeView;

/**
 * Class RenderBladeView
 *
 * Static class to hold the RenderBladeView instance.
 * Use this class to avoid direct calls to the render_blade view function and to allow for better testability and separation of concerns.
 */
class RenderBladeView implements RenderBladeViewInterface
{
    /**
     * @inheritDoc
     */
    public function renderBladeView($view, $data = [], $overrideViewPaths = false, $formatError = true): string
    {
        return \render_blade_view($view, $data, $overrideViewPaths, $formatError);
    }
}
