<?php

namespace Municipio\BackdropBanner\Block;

use Municipio\HooksRegistrar\Hookable;
use WpService\Contracts\__;
use WpService\Contracts\AddAction;
use WpService\Contracts\GetTemplateDirectoryUri;
use WpService\Contracts\RegisterBlockType;
use WpService\Contracts\WpRegisterScript;

class BackdropBannerBlock implements Hookable
{
    private const EDITOR_SCRIPT_HANDLE = 'municipio-backdrop-banner-editor';
    private const BLOCK_NAME = 'municipio/backdrop-banner';

    public function __construct(
        private AddAction&RegisterBlockType&WpRegisterScript&GetTemplateDirectoryUri&__ $wpService,
        private BackdropBannerRenderer $renderer,
    ) {}

    public function addHooks(): void
    {
        $this->wpService->addAction('init', [$this, 'registerBlock']);
    }

    public function registerBlock(): void
    {
        $this->wpService->wpRegisterScript(
            self::EDITOR_SCRIPT_HANDLE,
            $this->wpService->getTemplateDirectoryUri() . '/assets/dist/blocks/js/backdrop-banner-editor.js',
            ['wp-blocks', 'wp-element', 'wp-components', 'wp-block-editor', 'wp-hooks'],
            null,
        );

        $this->wpService->registerBlockType(
            self::BLOCK_NAME,
            [
                'title' => $this->wpService->__('Backdrop Banner'),
                'category' => 'theme',
                'editor_script' => self::EDITOR_SCRIPT_HANDLE,
                'render_callback' => [$this->renderer, 'render'],
                'attributes' => [
                    'title' => [
                        'type' => 'string',
                    ],
                ],
                'supports' => [
                    'autoRegister' => true,
                    'align' => ['wide', 'full'],
                    'color' => ['text' => true, 'background' => true],
                    'spacing' => ['margin' => true, 'padding' => true],
                ],
            ],
        );
    }
}
