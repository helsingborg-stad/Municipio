<?php

declare(strict_types=1);

namespace Municipio\Standards;

use AcfService\Implementations\FakeAcfService;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class DiscoveryFilesTest extends TestCase
{
    private array $fields = [];
    private string $administrationEmail = 'admin@example.test';

    #[TestDox('Registers the robots and security.txt hooks through WpService')]
    public function testRegistersHooks(): void
    {
        $wpService = $this->createWpService();
        $this->createDiscoveryFiles($wpService);

        static::assertSame('robots_txt', $wpService->methodCalls['addFilter'][0][0]);
        static::assertSame('parse_request', $wpService->methodCalls['addAction'][0][0]);
    }

    #[TestDox('Appends configured robots directives to WordPress output')]
    public function testRendersRobotsTxt(): void
    {
        $this->fields['robots_txt_directives'] = "User-agent: ExampleBot\nDisallow: /private/";

        $output = $this->createDiscoveryFiles()->renderRobotsTxt("User-agent: *\nDisallow:", true);

        static::assertSame(
            "User-agent: *\nDisallow:\n\n# Additional directives configured in Site files\nUser-agent: ExampleBot\nDisallow: /private/\n",
            $output,
        );
    }

    #[TestDox('Builds security.txt with configured values and safe defaults')]
    public function testBuildsSecurityTxt(): void
    {
        $this->fields = [
            'security_txt_contact_email' => 'security@example.test',
            'security_txt_policy' => 'https://example.test/security-policy',
        ];

        $output = $this->createDiscoveryFiles()->getSecurityTxtContent();

        static::assertStringContainsString("Contact: mailto:security@example.test\n", $output);
        preg_match('/Expires: (.+)/', $output, $matches);
        $expiresAt = new \DateTimeImmutable($matches[1]);

        static::assertSame(1, (int) $expiresAt->format('N'));
        static::assertSame('00:00:00', $expiresAt->format('H:i:s'));
        static::assertStringContainsString("Canonical: https://example.test/.well-known/security.txt\n", $output);
        static::assertStringContainsString("Policy: https://example.test/security-policy\n", $output);
    }

    #[TestDox('Uses the WordPress administration email when no security contact is configured')]
    public function testUsesAdministrationEmailAsSecurityContactDefault(): void
    {
        $output = $this->createDiscoveryFiles()->getSecurityTxtContent();

        static::assertStringContainsString("Contact: mailto:admin@example.test\n", $output);
    }

    #[TestDox('Uses an administrator account email when the administration email is unavailable')]
    public function testUsesAdministratorAccountEmailAsSecurityContactFallback(): void
    {
        $this->administrationEmail = '';

        $output = $this->createDiscoveryFiles()->getSecurityTxtContent();

        static::assertStringContainsString("Contact: mailto:administrator@example.test\n", $output);
    }

    #[TestDox('Recognizes the standard security.txt location and root fallback')]
    public function testRecognizesSecurityTxtPaths(): void
    {
        $discoveryFiles = $this->createDiscoveryFiles();

        static::assertTrue($discoveryFiles->isSecurityTxtRequest('.well-known/security.txt'));
        static::assertTrue($discoveryFiles->isSecurityTxtRequest('security.txt'));
        static::assertFalse($discoveryFiles->isSecurityTxtRequest('.well-known/other.txt'));
    }

    private function createDiscoveryFiles(?FakeWpService $wpService = null): DiscoveryFiles
    {
        $wpService ??= $this->createWpService();

        return new DiscoveryFiles(
            $wpService,
            new FakeAcfService([
                'getField' => fn (string $field): mixed => $this->fields[$field] ?? false,
            ]),
        );
    }

    private function createWpService(): FakeWpService
    {
        return new FakeWpService([
            'addAction' => true,
            'addFilter' => true,
            'getOption' => fn (string $option): string => $option === 'admin_email' ? $this->administrationEmail : '',
            'getUsers' => [(object) ['user_email' => 'administrator@example.test']],
            'homeUrl' => static fn (string $path): string => 'http://example.test' . $path,
            'sanitizeEmail' => static fn (string $email): string => $email,
            'setUrlScheme' => static fn (string $url, string $scheme): string => preg_replace('#^https?://#', $scheme . '://', $url) ?? $url,
            'wpStripAllTags' => static fn (string $value): string => strip_tags($value),
        ]);
    }
}
