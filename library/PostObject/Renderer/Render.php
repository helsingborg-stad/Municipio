<?php

namespace Municipio\PostObject\Renderer;

use ComponentLibrary\Init as ComponentLibraryInit;
use HelsingborgStad\BladeService\BladeServiceInterface;
use Municipio\Helper\TranslatedLabels;

class Render implements RenderInterface
{
    protected static ?BladeServiceInterface $bladeEngine = null;

    /**
     * Constructor.
     */
    public function __construct(
        private string $view,
        private array $config = []
    ) {
    }

    /**
     * @inheritDoc
     */
    public function render(): string
    {
        $this->maybeSetupBladeEngine();

        try {
            $markup = self::$bladeEngine->makeView($this->view, $this->getViewData(), [], $this->getViewPaths())->render();
        } catch (\Throwable $e) {
            $markup = self::$bladeEngine->errorHandler($e)->print();
        }

        return $markup ?? '';
    }

    /**
     * Setup blade engine.
     */
    private function maybeSetupBladeEngine(): void
    {
        if (is_null(self::$bladeEngine)) {
            $componentLibrary  = new ComponentLibraryInit([]);
            self::$bladeEngine = $componentLibrary->getEngine();
        }
    }

    /**
     * Get the view paths.
     *
     * @return array The view paths.
     */
    public function getViewPaths(): array
    {
        return [
            __DIR__ . '/PostObjectRenderer/Views/',
            __DIR__ . '/PostObjectCollectionRenderer/Views/'
        ];
    }

    /**
     * Get the view data.
     */
    private function getViewData(): array
    {
        $viewData = [ ...$this->config, 'lang' => $this->getLanguageObject() ];

        return $viewData;
    }

    /**
     * Get the language object.
     *
     * @return object The language object.
     */
    public function getLanguageObject(): object
    {
        return TranslatedLabels::getLang();
    }
}
