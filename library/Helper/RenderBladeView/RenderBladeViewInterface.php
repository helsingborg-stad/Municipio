<?php

namespace Municipio\Helper\RenderBladeView;

/**
 * Class RenderBladeView
 *
 * Static class to hold the RenderBladeView instance.
 * Use this class to avoid direct calls to the render_blade view function and to allow for better testability and separation of concerns.
 */
interface RenderBladeViewInterface
{
    /**
     * Renders a Blade view with the given data and view paths.
     *
     * @param string $view The name of the Blade view to render.
     * @param array $data An associative array of data to pass to the view.
     * @param bool|array $overrideViewPaths If true, uses the default view paths; if an array, overrides the default view paths.
     * @param bool $formatError If true, formats the error output if an exception occurs.
     * @return string The rendered HTML markup of the Blade view.
     * @throws \Throwable If an error occurs during rendering and $formatError is false.
     */
    public function renderBladeView($view, $data = [], $overrideViewPaths = false, $formatError = true): string;
}
