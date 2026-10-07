<?php

declare(strict_types=1);

namespace Municipio\Standards;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

function add_action(...$args): void
{
    DiscoveryFilesHookRecorder::$actions[] = $args;
}

function add_filter(...$args): void
{
    DiscoveryFilesHookRecorder::$filters[] = $args;
}

function get_field(string $field, string $context): mixed
{
    return DiscoveryFilesTest::$fields[$field] ?? null;
}

function get_option(string $option): string
{
    return $option === 'admin_email' ? DiscoveryFilesTest::$administrationEmail : '';
}

function get_users(array $arguments): array
{
    return [(object) ['user_email' => 'administrator@example.test']];
}

function sanitize_email(string $email): string
{
    return $email;
}

function wp_strip_all_tags(string $value): string
{
    return strip_tags($value);
}

function home_url(string $path): string
{
    return 'http://example.test' . $path;
}

function set_url_scheme(string $url, string $scheme): string
{
    return preg_replace('#^https?://#', $scheme . '://', $url) ?? $url;
}

class DiscoveryFilesHookRecorder
{
    public static array $actions = [];
    public static array $filters = [];
}

class DiscoveryFilesTest extends TestCase
{
    public static array $fields = [];
    public static string $administrationEmail = 'admin@example.test';

    protected function setUp(): void
    {
        self::$fields = [];
        self::$administrationEmail = 'admin@example.test';
        DiscoveryFilesHookRecorder::$actions = [];
        DiscoveryFilesHookRecorder::$filters = [];
    }

    #[TestDox('Registers the robots and security.txt hooks')]
    public function testRegistersHooks(): void
    {
        new DiscoveryFiles();

        $this->assertContains(['robots_txt', [new DiscoveryFiles(), 'renderRobotsTxt'], 10, 2], DiscoveryFilesHookRecorder::$filters);
        $this->assertContains(['parse_request', [new DiscoveryFiles(), 'maybeServeSecurityTxt'], 0], DiscoveryFilesHookRecorder::$actions);
    }

    #[TestDox('Appends configured robots directives to WordPress output')]
    public function testRendersRobotsTxt(): void
    {
        self::$fields['robots_txt_directives'] = "User-agent: ExampleBot\nDisallow: /private/";

        $output = (new DiscoveryFiles())->renderRobotsTxt("User-agent: *\nDisallow:", true);

        $this->assertSame(
            "User-agent: *\nDisallow:\n\n# Additional directives configured in Site files\nUser-agent: ExampleBot\nDisallow: /private/\n",
            $output,
        );
    }

    #[TestDox('Builds security.txt with configured values and safe defaults')]
    public function testBuildsSecurityTxt(): void
    {
        self::$fields = [
            'security_txt_contact_email' => 'security@example.test',
            'security_txt_policy' => 'https://example.test/security-policy',
        ];

        $output = (new DiscoveryFiles())->getSecurityTxtContent();

        $this->assertStringContainsString("Contact: mailto:security@example.test\n", $output);
        $this->assertMatchesRegularExpression('/Expires: \d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}Z/', $output);
        $this->assertStringContainsString("Canonical: https://example.test/.well-known/security.txt\n", $output);
        $this->assertStringContainsString("Policy: https://example.test/security-policy\n", $output);
    }

    #[TestDox('Uses the WordPress administration email when no security contact is configured')]
    public function testUsesAdministrationEmailAsSecurityContactDefault(): void
    {
        $output = (new DiscoveryFiles())->getSecurityTxtContent();

        $this->assertStringContainsString("Contact: mailto:admin@example.test\n", $output);
    }

    #[TestDox('Uses an administrator account email when the administration email is unavailable')]
    public function testUsesAdministratorAccountEmailAsSecurityContactFallback(): void
    {
        self::$administrationEmail = '';

        $output = (new DiscoveryFiles())->getSecurityTxtContent();

        $this->assertStringContainsString("Contact: mailto:administrator@example.test\n", $output);
    }

    #[TestDox('Recognizes the standard security.txt location and root fallback')]
    public function testRecognizesSecurityTxtPaths(): void
    {
        $discoveryFiles = new DiscoveryFiles();

        $this->assertTrue($discoveryFiles->isSecurityTxtRequest('.well-known/security.txt'));
        $this->assertTrue($discoveryFiles->isSecurityTxtRequest('security.txt'));
        $this->assertFalse($discoveryFiles->isSecurityTxtRequest('.well-known/other.txt'));
    }
}
