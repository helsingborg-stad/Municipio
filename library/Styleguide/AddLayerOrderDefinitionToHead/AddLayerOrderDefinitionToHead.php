<?php

declare(strict_types=1);


namespace Municipio\Styleguide\AddLayerOrderDefinitionToHead;

use Municipio\HooksRegistrar\Hookable;
use WpService\Contracts\AddAction;
use WpService\Contracts\AddFilter;

/**
 * Ensures the global CSS layer order is declared before layered styles are evaluated.
 */
class AddLayerOrderDefinitionToHead implements Hookable
{
    private ?string $styleTag = null;

    /**
     * @param AddAction&AddFilter $wpService WordPress service wrapper.
     */
    public function __construct(
        private AddAction&AddFilter $wpService,
        private ?string $distDirectory = null,
    ) {}

    /**
     * Registers the frontend markup filter and the earliest viable admin head hooks.
     */
    public function addHooks(): void
    {
        $this->wpService->addFilter('Municipio\MarkupProcessor', [$this, 'process']);
        $this->wpService->addAction('admin_print_styles', [$this, 'render'], 0);
        $this->wpService->addAction('login_head', [$this, 'render'], 0);
    }

    /**
     * Prepends the layer order declaration to the first head tag in rendered frontend markup.
     *
     * @param string $markup The rendered HTML markup.
     * @return string
     */
    public function process(string $markup): string
    {
        // Add as first child of head
        return preg_replace('/<head(.*?)>/', '<head$1>' . $this->getStyleTag(), $markup, 1);
    }

    /**
     * Prints the shared layer order declaration in admin and login heads.
     */
    public function render(): void
    {
        echo $this->getStyleTag();
    }
    /** Reads the build output of Styleguide's shared _layers.scss entry. */
    private function getStyleTag(): string
    {
        if ($this->styleTag !== null) {
            return $this->styleTag;
        }

        $directory = $this->distDirectory ?? dirname(__DIR__, 3) . '/assets/dist';
        $manifestPath = $directory . '/manifest.json';
        $manifest = is_file($manifestPath) ? json_decode((string) file_get_contents($manifestPath), true) : null;
        $file = $manifest['css/layer-order.css'] ?? null;
        if (!is_string($file) || !is_file($directory . '/' . $file)) {
            throw new \RuntimeException('Missing layer-order CSS. Run npm run build:municipio.');
        }
        return $this->styleTag = '<style>' . trim((string) file_get_contents($directory . '/' . $file)) . '</style>';
    }

}
