<?php

declare(strict_types=1);

namespace Municipio\ProgressReporter\UI;

use WpService\Contracts\AdminUrl;
use WpService\Contracts\EscAttr;
use WpService\Contracts\EscHtml;
use WpService\Contracts\EscUrl;
use WpService\Contracts\WpCreateNonce;
use WpService\Contracts\WpNonceUrl;

/**
 * Renders buttons handled by the global admin progress action script.
 */
class AdminProgressActionButton
{
    /**
     * Create an admin progress action button renderer.
     */
    public function __construct(
        private AdminUrl&EscAttr&EscHtml&EscUrl&WpCreateNonce&WpNonceUrl $wpService,
    ) {}

    /**
     * Render a nonce-protected GET progress action button.
     */
    public function renderGet(AdminProgressActionButtonConfig $config): string
    {
        $url = $this->createEndpoint($config);
        $url = $this->wpService->wpNonceUrl($url, $config->action);

        return $this->render($config, $url);
    }

    /**
     * Render a nonce-protected POST progress action button.
     */
    public function renderPost(AdminProgressActionButtonConfig $config): string
    {
        return $this->render(
            $config,
            $this->createEndpoint($config),
            'post',
            $this->wpService->wpCreateNonce($config->action),
        );
    }

    /**
     * Create the WordPress AJAX endpoint URL.
     */
    private function createEndpoint(AdminProgressActionButtonConfig $config): string
    {
        return $this->wpService->adminUrl('admin-ajax.php') . '?' . http_build_query([
            'action' => $config->action,
            ...$config->parameters,
        ]);
    }

    /**
     * Render escaped progress action markup.
     */
    private function render(
        AdminProgressActionButtonConfig $config,
        string $url,
        ?string $method = null,
        ?string $nonce = null,
    ): string {
        $attributes = [
            'type' => 'button',
            'class' => 'button button-primary',
            'data-js-progress-url' => $this->wpService->escUrl($url),
        ];

        if ($method !== null) {
            $attributes['data-js-progress-method'] = $method;
        }

        if ($nonce !== null) {
            $attributes['data-js-progress-nonce'] = $nonce;
        }

        if ($config->errorMessage !== null) {
            $attributes['data-js-progress-error-message'] = $config->errorMessage;
        }

        $markup = '<button';
        foreach ($attributes as $name => $value) {
            $escapedValue = $name === 'data-js-progress-url' ? $value : $this->wpService->escAttr($value);
            $markup .= sprintf(' %s="%s"', $name, $escapedValue);
        }

        if ($config->state === AdminProgressActionButtonState::Disabled) {
            $markup .= ' disabled';
        }

        return $markup . '>' . $this->wpService->escHtml($config->label) . '</button>';
    }
}