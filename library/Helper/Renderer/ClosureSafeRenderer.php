<?php

declare(strict_types=1);

namespace Municipio\Helper\Renderer;

use ComponentLibrary\Renderer\RendererInterface;
use HelsingborgStad\BladeService\BladeServiceInterface;

/**
 * Drop-in replacement for ComponentLibrary\Renderer\Renderer.
 *
 * The vendor renderer normalizes render data by converting every object to an
 * array via get_object_vars(), which strips all methods and any private or
 * unset public properties. This breaks Closures (turned into an empty array,
 * later throwing "Array callback must have exactly two elements" when called)
 * as well as plain objects like config/service objects and post objects
 * (turned into an array, later throwing "Call to a member function ... on
 * array" when a method is called on them). This renderer skips that
 * conversion entirely, since it isn't needed outside of the registered
 * '@component' directive system, which normalizes its own data separately.
 */
class ClosureSafeRenderer implements RendererInterface
{
    public function __construct(
        private BladeServiceInterface $bladeService,
    ) {}

    public function render(string $view, array $data = []): string
    {
        try {
            $markup = $this->bladeService->makeView(
                $view,
                array_merge($data, ['errorMessage' => false])
            )->render();
        } catch (\Throwable $e) {
            if (!defined('WP_DEBUG') || WP_DEBUG !== true) {
                throw $e;
            }

            $this->bladeService->errorHandler($e)->print();
            return '';
        }

        return $markup;
    }
}
