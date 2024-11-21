<?php

namespace Municipio\PostObject\PostObjectRenderer\Appearances;

use Municipio\PostObject\PostObjectInterface;
use Municipio\PostObject\PostObjectRenderer\PostObjectRendererInterface;
use ComponentLibrary\Init as ComponentLibraryInit;
use HelsingborgStad\BladeService\BladeServiceInterface;
use Municipio\Helper\TranslatedLabels;

/**
 * Render PostObject as a list item.
 */
abstract class PostObjectBladeRenderer implements PostObjectRendererInterface
{
    protected static ?BladeServiceInterface $bladeEngine = null;
    protected array $config                              = [];

    /**
     * Setup blade engine.
     */
    private function setupBladeEngine(): void
    {
        if (is_null(self::$bladeEngine)) {
            $componentLibrary  = new ComponentLibraryInit([]);
            self::$bladeEngine = $componentLibrary->getEngine();
        }
    }

    /**
     * @inheritDoc
     */
    public function setConfig(array $config): void
    {
        $this->config = $config;
    }

    /**
     * @inheritDoc
     */
    public function getConfig(): array
    {
        return $this->config;
    }

    /**
     * @inheritDoc
     */
    public function render(PostObjectInterface $postObject): string
    {
        return $this->renderView($this->getViewName(), $postObject);
    }

    /**
     * Get the view paths.
     *
     * @return array The view paths.
     */
    protected function getViewPaths(): array
    {
        return [__DIR__ . '/Views/'];
    }

    /**
     * Get the view data.
     */
    protected function getViewData(PostObjectInterface $postObject): array
    {
        return ['postObject' => $postObject, 'config' => $this->getConfig(), 'lang' => $this->getLanguageObject()];
    }

    /**
     * Get the language object.
     *
     * @return object The language object.
     */
    protected function getLanguageObject(): object
    {
        return TranslatedLabels::getLang();
    }

    /**
     * Render the view.
     *
     * @param string $view The rendered view.
     */
    protected function renderView(string $view, PostObjectInterface $postObject): string
    {
        $this->setupBladeEngine();

        try {
            $markup = self::$bladeEngine->makeView($view, $this->getViewData($postObject), [], $this->getViewPaths())->render();
        } catch (\Throwable $e) {
            $markup = self::$bladeEngine->errorHandler($e)->print();
        }

        return $markup ?? '';
    }

    /**
     * Get the view name.
     * @return string The view name.
     */
    abstract public function getViewName(): string;
}
