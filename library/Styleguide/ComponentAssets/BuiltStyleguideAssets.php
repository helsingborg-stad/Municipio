<?php

declare(strict_types=1);

namespace Municipio\Styleguide\ComponentAssets;

/** Reads the two files produced by the styleguide build. */
class BuiltStyleguideAssets
{
    private array $manifest;
    private array $utilityClassMap;

    public function __construct(string $distDirectory)
    {
        $this->manifest = $this->readJson($distDirectory . '/manifest.json');
        $this->utilityClassMap = $this->readJson($distDirectory . '/utility-class-map.json');
    }

    public function has(string $path): bool
    {
        return array_key_exists($path, $this->manifest);
    }

    public function utilityOrder(): array
    {
        return array_flip($this->utilityClassMap['order'] ?? []);
    }

    public function pathsForComponents(array $components, string $type): array
    {
        $paths = [];
        foreach (array_keys($components) as $slug) {
            $assetName = strtolower(explode('__', $slug, 2)[0]);
            $paths['component-' . $assetName] = $type . '/components/' . $assetName . '.' . $type;
        }
        return $paths;
    }

    public function utilitiesInMarkup(string $markup): array
    {
        $utilities = [];
        preg_match_all('/\\bclass=["\']([^"\']+)["\']/', $markup, $matches);
        foreach ($matches[1] as $classList) {
            foreach (preg_split('/\\s+/', $classList) as $className) {
                foreach ($this->utilityClassMap['classes'][$className] ?? [] as $utility) {
                    $utilities[$utility] = true;
                }
            }
        }
        return array_keys($utilities);
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
