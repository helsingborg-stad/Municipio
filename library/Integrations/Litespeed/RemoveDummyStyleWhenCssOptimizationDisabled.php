<?php

declare(strict_types=1);

namespace Municipio\Integrations\Litespeed;

use Municipio\HooksRegistrar\Hookable;
use WpService\Contracts\AddAction;
use WpService\Contracts\GetOption;
use WpService\Contracts\WpDequeueStyle;
use WpService\Contracts\WpDeregisterStyle;

/**
 * Removes LiteSpeed Cache's dummy stylesheet when no CSS optimization uses it.
 */
class RemoveDummyStyleWhenCssOptimizationDisabled implements Hookable
{
    private const OPTION_NAME = 'litespeed-cache-conf';
    private const STYLE_HANDLE = 'litespeed-cache-dummy';

    /**
     * Options that can cause LiteSpeed Cache to use the dummy stylesheet as an
     * insertion point for optimized CSS.
     *
     * @var list<string>
     */
    private const CSS_OPTIMIZATION_OPTIONS = [
        'optm-css_min',
        'optm-css_comb',
        'optm-css_async',
        'optm-ucss',
        'optm-ggfonts_rm',
        'optm-ggfonts_async',
        'optm-css_font_display',
    ];

    /**
     * @param AddAction&GetOption&WpDequeueStyle&WpDeregisterStyle $wpService WordPress service wrapper.
     */
    public function __construct(
        private AddAction&GetOption&WpDequeueStyle&WpDeregisterStyle $wpService,
    ) {}

    /**
     * Runs after LiteSpeed Cache has enqueued its placeholder stylesheet.
     */
    public function addHooks(): void
    {
        $this->wpService->addAction(
            'wp_enqueue_scripts',
            [$this, 'removeStyleWhenCssOptimizationIsDisabled'],
            PHP_INT_MAX,
        );
    }

    /**
     * Removes the placeholder only when no LiteSpeed CSS optimization is enabled.
     */
    public function removeStyleWhenCssOptimizationIsDisabled(): void
    {
        if ($this->hasEnabledCssOptimization()) {
            return;
        }

        $this->wpService->wpDequeueStyle(self::STYLE_HANDLE);
        $this->wpService->wpDeregisterStyle(self::STYLE_HANDLE);
    }

    /**
     * Determines whether any LiteSpeed setting can write optimized CSS to the placeholder.
     */
    private function hasEnabledCssOptimization(): bool
    {
        $options = $this->wpService->getOption(self::OPTION_NAME, []);

        if (!is_array($options)) {
            return true;
        }

        foreach (self::CSS_OPTIMIZATION_OPTIONS as $option) {
            if (filter_var($options[$option] ?? false, FILTER_VALIDATE_BOOLEAN)) {
                return true;
            }
        }

        return false;
    }
}
