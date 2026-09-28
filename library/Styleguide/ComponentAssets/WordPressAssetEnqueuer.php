<?php

declare(strict_types=1);

namespace Municipio\Styleguide\ComponentAssets;

use ComponentLibrary\Assets\AssetEnqueuerInterface;
use WpService\WpService;
use WpUtilService\Features\Enqueue\EnqueueManagerInterface;

/** Collects rendered component assets and prints them into the completed document. */
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

    public static function setInstance(self $instance): void
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

    /** WordPress has already run wp_head() when Municipio renders its Blade view. */
    public function injectIntoMarkup(string $markup): string
    {
        if (!str_contains($markup, '</head>') || !str_contains($markup, '</body>')) {
            return $markup;
        }

        $this->utilities += array_fill_keys($this->builtAssets->utilitiesInMarkup($markup), true);
        $stylePaths = array_merge($this->styles, $this->builtAssets->pathsForComponents($this->components, 'css'));
        $scriptPaths = array_merge($this->scripts, $this->builtAssets->pathsForComponents($this->components, 'js'));

        $order = $this->builtAssets->utilityOrder();
        uksort($this->utilities, static fn(string $a, string $b): int =>
            ($order[$a] ?? PHP_INT_MAX) <=> ($order[$b] ?? PHP_INT_MAX));
        foreach (array_keys($this->utilities) as $name) {
            $stylePaths['utility-' . $name] = 'css/utilities/' . $name . '.css';
        }

        $styleHandles = $this->addAssets($stylePaths, 'css');
        $scriptHandles = $this->addAssets($scriptPaths, 'js');

        $styleTags = '';
        if ($styleHandles !== []) {
            ob_start();
            $this->wpService->wpPrintStyles($styleHandles);
            $styleTags = (string) ob_get_clean();
        }

        $scriptTags = '';
        if ($scriptHandles !== []) {
            ob_start();
            $this->wpService->wpPrintScripts($scriptHandles);
            $scriptTags = (string) ob_get_clean();
        }

        $markup = str_replace('</head>', $styleTags . '</head>', $markup);
        return str_replace('</body>', $scriptTags . '</body>', $markup);
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
