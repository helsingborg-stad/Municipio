<?php

declare(strict_types=1);

namespace Municipio\Styleguide\Customize\RestApi\Support;

use Municipio\Styleguide\Customize\RestApi\Config\CustomizeConfigInterface;
use PHPUnit\Framework\TestCase;
use WpService\Contracts\GetPostMeta;
use WpService\Contracts\GetThemeMod;

class CustomizeTokensReaderTest extends TestCase
{
    public function testReadReturnsChangesetMetaWhenChangesetExists(): void
    {
        $wpService = $this->createWpService('theme-mod-value', '{"changeset":true}');
        $config = $this->createConfig();
        $resolver = $this->createResolver(999);
        $reader = new CustomizeTokensReader($wpService, $config, $resolver);

        $this->assertSame('{"changeset":true}', $reader->read($this->createMock(\WP_REST_Request::class)));
    }

    public function testReadFallsBackToThemeModWhenNoChangesetExists(): void
    {
        $wpService = $this->createWpService('{"theme":true}', '{"changeset":true}');
        $config = $this->createConfig();
        $resolver = $this->createResolver(null);
        $reader = new CustomizeTokensReader($wpService, $config, $resolver);

        $this->assertSame('{"theme":true}', $reader->read($this->createMock(\WP_REST_Request::class)));
    }

    public function testReadReturnsNullWhenNoStringPayloadExists(): void
    {
        $wpService = $this->createWpService(['invalid'], ['invalid']);
        $config = $this->createConfig();
        $resolver = $this->createResolver(100);
        $reader = new CustomizeTokensReader($wpService, $config, $resolver);

        $this->assertNull($reader->read($this->createMock(\WP_REST_Request::class)));
    }

    private function createConfig(): CustomizeConfigInterface
    {
        $config = $this->createMock(CustomizeConfigInterface::class);
        $config->method('getThemeModKey')->willReturn('tokens');

        return $config;
    }

    private function createResolver(?int $changesetId): ChangesetIdResolverInterface
    {
        $resolver = $this->createMock(ChangesetIdResolverInterface::class);
        $resolver->method('resolve')->willReturn($changesetId);

        return $resolver;
    }

    private function createWpService(mixed $themeModValue, mixed $postMetaValue): GetThemeMod&GetPostMeta
    {
        return new class($themeModValue, $postMetaValue) implements GetThemeMod, GetPostMeta {
            public function __construct(
                private mixed $themeModValue,
                private mixed $postMetaValue,
            ) {}

            public function getThemeMod(string $name, mixed $defaultValue = false): mixed
            {
                return $this->themeModValue;
            }

            public function getPostMeta(int $postId, string $key = '', bool $single = false): mixed
            {
                return $this->postMetaValue;
            }
        };
    }
}
