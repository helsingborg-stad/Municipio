<?php

declare(strict_types=1);

namespace Municipio\Styleguide\ComponentAssets;

use ComponentLibrary\Assets\AssetEnqueuerInterface;
use WpService\WpService;
use WpUtilService\Features\Enqueue\EnqueueManagerInterface;

class WordPressAssetEnqueuer implements AssetEnqueuerInterface
{
    private static ?self $instance = null;
    private array $components = [];
    private array $utilities = [];
    private array $styles = [];
    private array $scripts = [];
    private array $manifest;
    private array $utilityMap;

    public function __construct(
        private EnqueueManagerInterface $enqueue,
        private WpService $wpService,
        string $distDirectory,
    ) {
        $this->manifest = $this->readJson($distDirectory . '/manifest.json');
        $this->utilityMap = $this->readJson($distDirectory . '/utility-class-map.json');
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
        foreach ($dependencies['sass']['components'] ?? [] as $component) {
            if (is_string($component)) {
                $this->components[$component] = true;
            }
        }
        $this->components[$slug] = true;
        foreach ($dependencies['utilities'] ?? [] as $utility) {
            if (is_string($utility)) {
                $this->enqueueUtility($utility);
            }
        }
    }

    public function enqueueUtility(string $name): void
    {
        $this->utilities[$name] = true;
    }

    public function enqueueStyle(string $handle, string $url): void
    {
        $this->styles[$handle] = ltrim($url, '/');
    }

    public function enqueueScript(string $handle, string $url): void
    {
        $this->scripts[$handle] = ltrim($url, '/');
    }

    public function renderStyles(string $bodyMarkup): string
    {
        preg_match_all('/\\bclass=["\']([^"\']+)["\']/', $bodyMarkup, $matches);
        foreach ($matches[1] as $classList) {
            foreach (preg_split('/\\s+/', $classList) as $className) {
                foreach ($this->utilityMap['classes'][$className] ?? [] as $utility) {
                    $this->enqueueUtility($utility);
                }
            }
        }

        $paths = $this->styles + $this->componentPaths('css');
        $order = array_flip($this->utilityMap['order'] ?? []);
        uksort($this->utilities, static fn(string $a, string $b): int =>
            ($order[$a] ?? PHP_INT_MAX) <=> ($order[$b] ?? PHP_INT_MAX));
        foreach (array_keys($this->utilities) as $utility) {
            $paths['utility-' . $utility] = 'css/utilities/' . $utility . '.css';
        }
        $handles = $this->addAssets($paths);
        if ($handles === []) {
            return '';
        }
        ob_start();
        $this->wpService->wpPrintStyles($handles);
        return (string) ob_get_clean();
    }

    public function renderScripts(): string
    {
        $handles = $this->addAssets($this->scripts + $this->componentPaths('js'));
        if ($handles === []) {
            return '';
        }
        ob_start();
        $this->wpService->wpPrintScripts($handles);
        return (string) ob_get_clean();
    }

    private function componentPaths(string $type): array
    {
        $paths = [];
        foreach (array_keys($this->components) as $slug) {
            $name = strtolower(explode('__', $slug, 2)[0]);
            $paths['component-' . $name] = $type . '/components/' . $name . '.' . $type;
        }
        return $paths;
    }

    private function addAssets(array $paths): array
    {
        $handles = [];
        foreach (array_unique($paths) as $path) {
            if (!isset($this->manifest[$path])) {
                continue;
            }
            $this->enqueue->add($path, [], null, str_ends_with($path, '.js'));
            $normalized = strtolower(str_replace(['\\', '/', '_'], '-', $path));
            $handles[] = pathinfo($normalized, PATHINFO_FILENAME) . pathinfo($normalized, PATHINFO_EXTENSION);
        }
        return $handles;
    }

    private function readJson(string $path): array
    {
        if (!is_file($path)) {
            return [];
        }
        $data = json_decode((string) file_get_contents($path), true);
        return is_array($data) ? $data : [];
    }
}
