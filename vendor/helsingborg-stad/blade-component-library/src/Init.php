<?php

namespace BladeComponentLibrary;

use HelsingborgStad\GlobalBladeEngine as Blade;

class Init {
    public function __construct(
        $paths = array(
            'viewPaths' => array(),
            'controllerPaths' => array(),
            'internalComponentsPath' => array(),
        )
    ) {
        // Add view path to renderer
        // In this case all components, their controller and view path are located under the same folder structure.
        // This may differ in a Wordpress child implementation.
        $internalPaths = array(
            __DIR__ . DIRECTORY_SEPARATOR . 'Component' . DIRECTORY_SEPARATOR,
        );

        // Initialize all view paths so that this library is last
        $viewPaths = array_unique(
            array_merge($paths['viewPaths'], $internalPaths)
        );
        if (function_exists('apply_filters')) {
            $viewPaths = apply_filters(
                'helsingborg-stad/blade/viewPaths',
                $viewPaths
            );
        }
        foreach ($viewPaths as $path) {
            Blade::addViewPath(rtrim($path, DIRECTORY_SEPARATOR));
        }

        // Initialize all controller paths so that this library is last
        $controllerPaths = array_unique(
            array_merge($paths['controllerPaths'], $internalPaths)
        );
        if (function_exists('apply_filters')) {
            $controllerPaths = apply_filters(
                'helsingborg-stad/blade/controllerPaths',
                $controllerPaths
            );
        }
        foreach ($controllerPaths as $path) {
            Register::addControllerPath(
                rtrim($path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR
            );
        }

        // Initialize all internal components paths so that this library is last
        $internalComponentsPath = array_unique(
            array_merge($paths['internalComponentsPath'], $internalPaths)
        );
        if (function_exists('apply_filters')) {
            $internalComponentsPath = apply_filters(
                'helsingborg-stad/blade/internalComponentsPath',
                $internalComponentsPath
            );
        }
        foreach ($internalComponentsPath as $path) {
            Register::registerInternalComponents(
                rtrim($path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR
            );
        }
    }
}
