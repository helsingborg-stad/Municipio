<?php

declare(strict_types=1);

namespace Municipio\AppleWallet;

use PHPUnit\Framework\TestCase;
use RuntimeException;

/** Tests Apple Wallet pass data creation from a category template. */
class ApplePassDataFactoryTest extends TestCase
{
    private string $templateDirectory;

    /** Creates a category template with configured and caller-provided placeholders. */
    protected function setUp(): void
    {
        parent::setUp();
        $this->templateDirectory = sys_get_temp_dir() . '/apple-wallet-template-' . bin2hex(random_bytes(8));
        mkdir($this->templateDirectory . '/example', 0o700, true);
        file_put_contents($this->templateDirectory . '/example/pass.json', json_encode([
            'serialNumber' => '{{serial}}',
            'teamIdentifier' => '{{APPLE_TEAM_ID}}',
            'passTypeIdentifier' => '{{APPLE_PASS_TYPE_IDENTIFIER}}',
            'storeCard' => ['secondaryFields' => [['key' => 'name', 'value' => 'Hello {{name}}']]],
        ], JSON_THROW_ON_ERROR));
    }

    /** Removes the temporary template. */
    protected function tearDown(): void
    {
        unlink($this->templateDirectory . '/example/pass.json');
        rmdir($this->templateDirectory . '/example');
        rmdir($this->templateDirectory);
        parent::tearDown();
    }

    /** Verifies that configured identifiers and caller placeholders are filled in every string value. */
    public function testFillsConfiguredAndCallerPlaceholders(): void
    {
        $passData = $this->createFactory()->create('example', [
            '{{serial}}' => 'card-1',
            '{{name}}' => 'Ada',
        ]);

        static::assertSame('card-1', $passData['serialNumber']);
        static::assertSame('TEAM12345', $passData['teamIdentifier']);
        static::assertSame('pass.example.test', $passData['passTypeIdentifier']);
        static::assertSame('Hello Ada', $passData['storeCard']['secondaryFields'][0]['value']);
        static::assertStringNotContainsString('{{', json_encode($passData, JSON_THROW_ON_ERROR));
    }

    /** Verifies that an unknown category is rejected. */
    public function testRejectsUnknownCategory(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('No Apple Wallet pass template exists for this category.');

        $this->createFactory()->create('missing', []);
    }

    /** @return ApplePassDataFactory A factory using the temporary template and a fixed Apple configuration. */
    private function createFactory(): ApplePassDataFactory
    {
        $config = $this->createMock(ApplePassCardConfig::class);
        $config->method('getPassTypeIdentifier')->willReturn('pass.example.test');
        $config->method('getTeamIdentifier')->willReturn('TEAM12345');

        return new ApplePassDataFactory($config, new ApplePassTemplateLoader($this->templateDirectory));
    }
}
