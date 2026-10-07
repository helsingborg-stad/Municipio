<?php

declare(strict_types=1);

namespace Municipio\Standards;

use DateTimeImmutable;
use DateTimeZone;

/**
 * Serves and configures the site's machine-readable discovery files.
 *
 * @mago-expect lint:too-many-methods Small helpers make each field's output validation explicit.
 */
class DiscoveryFiles
{
    public function __construct()
    {
        add_filter('robots_txt', [$this, 'renderRobotsTxt'], 10, 2);
        add_action('parse_request', [$this, 'maybeServeSecurityTxt'], 0);
    }

    /**
     * Adds administrator-configured directives to WordPress' generated robots.txt.
     *
     * WordPress is kept as the source of the base output so its privacy setting and
     * sitemap integration continue to work as expected.
     *
     * @param string $output The WordPress generated output.
     * @param bool   $public Whether search engines should index the site.
     * @mago-expect lint:no-boolean-flag-parameter This callback signature is prescribed by WordPress.
     */
    public function renderRobotsTxt(string $output, bool $public): string
    {
        $directives = $this->getOption('robots_txt_directives');

        if (!is_string($directives) || trim($directives) === '') {
            return $output;
        }

        $directives = $this->sanitizeMultilineValue($directives);

        if ($directives === '') {
            return $output;
        }

        return rtrim($output) . "\n\n# Additional directives configured in Site files\n" . $directives . "\n";
    }

    /**
     * Responds to both the RFC 9116 location and its common root fallback.
     *
     * @param object $wp The parsed WordPress request.
     */
    public function maybeServeSecurityTxt(object $wp): void
    {
        if (!$this->isSecurityTxtRequest((string) ($wp->request ?? ''))) {
            return;
        }

        status_header(200);
        nocache_headers();
        header('Content-Type: text/plain; charset=utf-8');
        header('X-Content-Type-Options: nosniff');

        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'HEAD') {
            echo $this->getSecurityTxtContent();
        }

        exit;
    }

    /**
     * Checks whether a parsed request maps to a supported security.txt location.
     */
    public function isSecurityTxtRequest(string $request): bool
    {
        return in_array(trim($request, '/'), ['.well-known/security.txt', 'security.txt'], true);
    }

    /**
     * Builds a RFC 9116 compatible security.txt document.
     */
    public function getSecurityTxtContent(): string
    {
        $lines = [];

        $lines[] = 'Contact: ' . $this->getContact();

        $lines[] = 'Expires: ' . $this->getExpiresAt();

        $lines[] = 'Canonical: ' . set_url_scheme(home_url('/.well-known/security.txt'), 'https');

        $policyUrl = $this->getHttpsUrlOption('security_txt_policy');
        if ($policyUrl !== '') {
            $lines[] = 'Policy: ' . $policyUrl;
        }

        return implode("\n", $lines) . "\n";
    }

    private function getContact(): string
    {
        $email = sanitize_email($this->sanitizeSingleLineValue((string) $this->getOption('security_txt_contact_email')));
        if ($email !== '') {
            return 'mailto:' . $email;
        }

        $adminEmail = sanitize_email((string) get_option('admin_email'));

        if ($adminEmail === '') {
            $administrators = get_users([
                'role' => 'administrator',
                'number' => 1,
            ]);
            $adminEmail = sanitize_email((string) ($administrators[0]->user_email ?? ''));
        }

        return $adminEmail !== '' ? 'mailto:' . $adminEmail : '';
    }

    private function getExpiresAt(): string
    {
        return (new DateTimeImmutable('now', new DateTimeZone('UTC')))
            ->modify('+364 days')
            ->format('Y-m-d\\TH:i:s\\Z');
    }

    private function getHttpsUrlOption(string $field): string
    {
        $value = $this->sanitizeSingleLineValue((string) $this->getOption($field));

        return $this->isHttpsUri($value) ? $value : '';
    }

    private function getOption(string $field): mixed
    {
        return function_exists('get_field') ? get_field($field, 'option') : null;
    }

    private function isHttpsUri(string $value): bool
    {
        return (bool) preg_match('#^https://#i', $value);
    }

    private function sanitizeMultilineValue(string $value): string
    {
        $lines = preg_split('/\r\n|\r|\n/', $value) ?? [];
        $lines = array_map($this->sanitizeSingleLineValue(...), $lines);

        return trim(implode("\n", array_filter($lines, static fn (string $line): bool => $line !== '')));
    }

    private function sanitizeSingleLineValue(string $value): string
    {
        return trim(wp_strip_all_tags(str_replace(["\r", "\n"], '', $value)));
    }
}
