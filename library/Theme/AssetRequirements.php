<?php

declare(strict_types=1);

namespace Municipio\Theme;

use WpUtilService\Features\Enqueue\EnqueueManagerInterface;

/**
 * Maps semantic frontend requirements to built assets.
 *
 * Controllers and renderers request capabilities rather than knowing Vite
 * entrypoint paths. Requests are idempotent, so a template controller and a
 * block renderer can safely require the same capability.
 */
class AssetRequirements
{
    private static ?self $instance = null;

    /** @var array<string, true> */
    private array $requested = [];

    /** @var list<string> */
    private array $styleHandles = [];

    /** @var list<string> */
    private array $scriptHandles = [];

    public function __construct(private EnqueueManagerInterface $enqueue)
    {
    }

    public static function setInstance(?self $instance): void
    {
        self::$instance = $instance;
    }

    public static function instance(): ?self
    {
        return self::$instance;
    }

    public static function requireAsset(string $requirement): void
    {
        self::$instance?->add($requirement);
    }

    public function add(string $requirement): void
    {
        if (array_key_exists($requirement, $this->requested)) {
            return;
        }

        $this->requested[$requirement] = true;

        match ($requirement) {
            'shell' => $this->enqueueShell(),
            'comments' => $this->enqueueComments(),
            'posts-list' => $this->enqueuePostsList(),
            default => throw new \InvalidArgumentException(
                sprintf('Unknown Municipio asset requirement: %s', $requirement),
            ),
        };
    }

    /**
     * Prints requirements discovered while a Blade section was rendered.
     *
     * Municipio captures wp_footer() before Blade renders post content. This
     * closes that timing gap for blocks that declare an asset while rendering.
     */
    public function renderLateScripts(): string
    {
        if ($this->scriptHandles === []) {
            return '';
        }

        ob_start();
        wp_print_scripts($this->scriptHandles);
        return (string) ob_get_clean();
    }

    /**
     * Prints styles found while rendering a Blade section into the styles stack.
     */
    public function renderLateStyles(): string
    {
        if ($this->styleHandles === []) {
            return '';
        }

        ob_start();
        wp_print_styles($this->styleHandles);
        return (string) ob_get_clean();
    }

    private function enqueueShell(): void
    {
        $this->enqueue->add('js/municipio-shell.js', ['jquery', 'wp-api-fetch']);
        $this->enqueue->add('css/municipio.css');
        $this->scriptHandles[] = 'js-municipio-shelljs';
        $this->styleHandles[] = 'css-municipiocss';
    }

    private function enqueueComments(): void
    {
        $this->enqueue->add('js/municipio-comments.js');
        $this->enqueue->add('css/municipio-comments.css');
        $this->scriptHandles[] = 'js-municipio-commentsjs';
        $this->styleHandles[] = 'css-municipio-commentscss';
    }

    private function enqueuePostsList(): void
    {
        $this->enqueue->add('js/municipio-posts-list.js', ['wp-api-fetch']);
        $this->scriptHandles[] = 'js-municipio-posts-listjs';
    }
}
