<?php

declare(strict_types=1);

namespace Municipio\Styleguide\ComponentAssets;

use ComponentLibrary\Assets\AssetEnqueuerInterface;
use WpService\WpService;
use WpUtilService\Features\Enqueue\EnqueueManagerInterface;

/** Collects rendered component assets for the layout's Blade stacks. */
class WordPressAssetEnqueuer implements AssetEnqueuerInterface
{
    private static ?self $instance = null;

    private array $components = [];
    private array $utilities = [];
    private array $styles = [];
    private array $scripts = [];
    private BuiltStyleguideAssets $builtAssets;

    public function __construct(
        private EnqueueManagerInterface $enqueue,
        private WpService $wpService,
        string $distDirectory,
    ) {
        $this->builtAssets = new BuiltStyleguideAssets($distDirectory);
    }

    public static function setInstance(?self $instance): void
    {
        self::$instance = $instance;
    }

    public static function instance(): ?self
    {
        return self::$instance;
    }

    public function enqueueComponent(string $slug, array $dependencies = []): void
    {
        $componentDependencies = array_filter($dependencies['sass']['components'] ?? [], 'is_string');
        $this->components += array_fill_keys($componentDependencies, true);
        $this->components[$slug] = true;
        $utilityDependencies = array_filter($dependencies['utilities'] ?? [], 'is_string');
        $this->utilities += array_fill_keys($utilityDependencies, true);
    }

    public function enqueueUtility(string $name): void
    {
        $this->utilities[$name] = true;
    }

    /** URLs are asset paths relative to the built styleguide directory. */
    public function enqueueStyle(string $handle, string $url): void
    {
        $this->styles[$handle] = ltrim($url, '/');
    }

    /** URLs are asset paths relative to the built styleguide directory. */
    public function enqueueScript(string $handle, string $url): void
    {
        $this->scripts[$handle] = ltrim($url, '/');
    }

    /** Build the styles pushed into the head after the body section has rendered. */
    public function renderStyles(string $bodyMarkup): string
    {
        $this->utilities += array_fill_keys($this->builtAssets->utilitiesInMarkup($bodyMarkup), true);
        $stylePaths = array_merge($this->styles, $this->builtAssets->pathsForComponents($this->components, 'css'));

        $order = $this->builtAssets->utilityOrder();
        uksort($this->utilities, static fn(string $a, string $b): int =>
            ($order[$a] ?? PHP_INT_MAX) <=> ($order[$b] ?? PHP_INT_MAX));
        foreach (array_keys($this->utilities) as $name) {
            $stylePaths['utility-' . $name] = 'css/utilities/' . $name . '.css';
        }

        $styleHandles = $this->addAssets($stylePaths, 'css');
        if ($styleHandles === []) {
            return '';
        }
        ob_start();
        $this->wpService->wpPrintStyles($styleHandles);
        return (string) ob_get_clean();
    }

    /** Build the scripts pushed after the body content. */
    public function renderScripts(): string
    {
        $scriptPaths = array_merge($this->scripts, $this->builtAssets->pathsForComponents($this->components, 'js'));
        $scriptHandles = $this->addAssets($scriptPaths, 'js');
        if ($scriptHandles === []) {
            return '';
        }
        ob_start();
        $this->wpService->wpPrintScripts($scriptHandles);
        return (string) ob_get_clean();
    }

    private function addAssets(array $paths, string $type): array
    {
        $handles = [];
        foreach (array_unique($paths) as $path) {
            if (!$this->builtAssets->has($path)) {
                continue;
            }
            $this->enqueue->add($path, [], null, $type === 'js');
            $normalizedPath = strtolower(str_replace(['\\', '/', '_'], '-', $path));
            $handles[] = pathinfo($normalizedPath, PATHINFO_FILENAME) . pathinfo($normalizedPath, PATHINFO_EXTENSION);
        }
        return $handles;
    }

}
