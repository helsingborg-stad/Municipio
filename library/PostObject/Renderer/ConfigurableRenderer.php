<?php

namespace Municipio\PostObject\Renderer;

use ComponentLibrary\Init as ComponentLibraryInit;
use HelsingborgStad\BladeService\BladeServiceInterface;
use Municipio\Helper\TranslatedLabels;
use Municipio\PostObject\PostObjectInterface;
use Municipio\PostObject\Renderer\ConfigurableRendererInterface;

/**
 * Render PostObject as a list item.
 */
abstract class ConfigurableRenderer implements ConfigurableRendererInterface
{
    protected static ?BladeServiceInterface $bladeEngine = null;
    protected array $config                              = [];
    protected PostObjectInterface $postObject;

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
    public function render(): string
    {
        $this->maybeSetupBladeEngine();

        try {
            $markup = self::$bladeEngine->makeView($this->getViewName(), $this->getViewData(), [], $this->getViewPaths())->render();
        } catch (\Throwable $e) {
            $markup = self::$bladeEngine->errorHandler($e)->print();
        }

        return $markup ?? '';
    }

    /**
     * Get the view paths.
     *
     * @return array The view paths.
     */
    public function getViewPaths(): array
    {
        return [__DIR__ . '/Views/'];
    }

    /**
     * Get the view data.
     */
    public function getViewData(): array
    {
        return ['config' => $this->getConfig(), 'lang' => $this->getLanguageObject()];
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

    /**
     * Get the view name.
     * @return string The view name.
     */
    abstract public function getViewName(): string;
}
