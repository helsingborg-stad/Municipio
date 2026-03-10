<?php

namespace Municipio\BackdropBanner;

use Municipio\HooksRegistrar\Hookable;
use WpService\Contracts\AddAction;
use WpService\Contracts\RegisterBlockType;
use WpService\Contracts\WpDequeueScript;
use WpService\Contracts\WpDeregisterScript;

class BackdropBanner implements Hookable
{
    public function __construct(
        private AddAction&RegisterBlockType&WpDequeueScript&WpDeregisterScript $wpService,
    ) {}

    public function addHooks(): void
    {
        $this->wpService->addAction('init', [$this, 'registerBlock']);
        $this->wpService->addAction('customize_controls_enqueue_scripts', [$this, 'excludeFromCustomizer'], 1);
    }

    private function getBlockJsonPath(): string
    {
        return __DIR__ . '/block.json';
    }

    public function registerBlock(): void
    {
        $this->wpService->registerBlockType($this->getBlockJsonPath());
    }

    public function excludeFromCustomizer(): void
    {
        $scriptHandle = $this->getEditorScriptHandle();
        $this->wpService->wpDequeueScript($scriptHandle);
        $this->wpService->wpDeregisterScript($scriptHandle);
    }

    private function getEditorScriptHandle(): string
    {
        $blockJson = json_decode(file_get_contents($this->getBlockJsonPath()), true);
        $blockName = $blockJson['name'] ?? 'municipio/backdrop-banner-block';
        $handle = str_replace('/', '-', $blockName) . '-editor-script';
        return $handle;
    }
}
