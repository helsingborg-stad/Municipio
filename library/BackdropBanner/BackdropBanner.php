<?php

namespace Municipio\BackdropBanner;

use Municipio\HooksRegistrar\Hookable;
use WpService\Contracts\AddAction;
use WpService\Contracts\GetTemplateDirectoryUri;
use WpService\Contracts\RegisterBlockType;
use WpService\Contracts\WpDequeueScript;
use WpService\Contracts\WpDeregisterScript;
use WpService\Contracts\WpRegisterScript;
use WpService\Contracts\WpRegisterStyle;

class BackdropBanner implements Hookable
{
    private const VIEW_SCRIPT_HANDLE = 'municipio-backdrop-banner-script';
    private const VIEW_STYLE_HANDLE = 'municipio-backdrop-banner-style';
    private const EDITOR_STYLE_HANDLE = 'municipio-backdrop-banner-style-editor';

    public function __construct(
        private AddAction&RegisterBlockType&WpDequeueScript&WpDeregisterScript&GetTemplateDirectoryUri&WpRegisterScript&WpRegisterStyle $wpService,
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

    private function getInnerBlockJsonPath(): string
    {
        return __DIR__ . '/Row/block.json';
    }

    public function registerBlock(): void
    {
        $this->registerScriptAndStyles();

        $this->wpService->registerBlockType($this->getBlockJsonPath());

        $this->wpService->registerBlockType($this->getInnerBlockJsonPath());
    }

    public function registerScriptAndStyles(): void
    {
        $this->wpService->wpRegisterScript(
            self::VIEW_SCRIPT_HANDLE,
            $this->wpService->getTemplateDirectoryUri() . '/assets/dist/' . \Municipio\Helper\CacheBust::name('js/backdrop-banner.js'),
            [],
            null,
            true,
        );

        $this->wpService->wpRegisterStyle(
            self::VIEW_STYLE_HANDLE,
            $this->wpService->getTemplateDirectoryUri() . '/assets/dist/' . \Municipio\Helper\CacheBust::name('css/backdrop-banner.css'),
            [],
            null,
        );
        
        $this->wpService->wpRegisterStyle(
            self::EDITOR_STYLE_HANDLE,
            $this->wpService->getTemplateDirectoryUri() . '/assets/dist/' . \Municipio\Helper\CacheBust::name('css/backdrop-banner-editor.css'),
            [],
            null,
        );
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
