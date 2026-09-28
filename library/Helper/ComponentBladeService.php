<?php

declare(strict_types=1);

namespace Municipio\Helper;

use ComponentLibrary\Init;
use ComponentLibrary\Renderer\BladeService\BladeServiceFactory;
use HelsingborgStad\BladeService\BladeServiceInterface;
use Municipio\Styleguide\ComponentAssets\WordPressAssetEnqueuer;
use WpService\WpService;

/** Creates Blade services with the request's component asset enqueuer. */
class ComponentBladeService
{
    public static function create(array $viewPaths, ?WpService $wpService = null): BladeServiceInterface
    {
        if (WordPressAssetEnqueuer::instance() === null && $wpService !== null) {
            return (new BladeServiceFactory($wpService))->create($viewPaths);
        }
        return (new Init($viewPaths, WordPressAssetEnqueuer::instance()))->getEngine();
    }
}
