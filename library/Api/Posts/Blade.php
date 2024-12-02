<?php

namespace Municipio\Api\Posts;

use ComponentLibrary\Init as ComponentLibraryInit;
use HelsingborgStad\BladeService\BladeServiceInterface;
use WpService\Contracts\ApplyFilters;

class Blade
{
    private BladeServiceInterface $bladeEngine;
    private array $viewPaths;

    public function __construct(private ComponentLibraryInit $componentLibrary, private ApplyFilters $wpService)
    {
        $this->viewPaths = $this->wpService->applyFilters('Municipio/Api/Posts/Appearances', []);

        $this->bladeEngine = $this->componentLibrary->getEngine();
    }

    public function render($view, $data = [], $compress = true)
    {
        $markup = '';
        $data = array_merge($data, array('errorMessage' => false));

        try {
            $markup = $this->bladeEngine->makeView($view, $data, [], $this->viewPaths)->render();
        } catch (\Throwable $e) {
            $this->bladeEngine->errorHandler($e)->print();
        }

        if ($compress == true) {
            $replacements = array(
                ["~<!--(.*?)-->~s", ""],
                ["/\r|\n/", ""],
                ["!\s+!", " "]
            );

            foreach ($replacements as $replacement) {
                $markup = preg_replace($replacement[0], $replacement[1], $markup);
            }

            return $markup;
        }

        return $markup;
    }
}