<?php

declare(strict_types=1);

namespace Municipio\Styleguide\Customize\RestApi\Support;

use Municipio\Styleguide\Customize\RestApi\Config\CustomizeConfigInterface;
use PHPUnit\Framework\TestCase;
use WpService\Contracts\SetThemeMod;
use WpService\Contracts\UpdatePostMeta;
use WpService\Contracts\WpSavePostRevision;

class CustomizeTokensWriterTest extends TestCase
{
    public function testWriteStoresToChangesetAndSavesRevisionWhenChangesetExists(): void
    {
        $wpService = $this->createWpService(true, true);
        $config = $this->createConfig();
        $resolver = $this->createResolver(123);
        $writer = new CustomizeTokensWriter($wpService, $config, $resolver);

        $result = $writer->write($this->createMock(\WP_REST_Request::class), '{"a":1}');

        $this->assertTrue($result);
        $this->assertSame(123, $wpService->updatedPostId);
        $this->assertSame(123, $wpService->revisionSavedForPostId);
        $this->assertNull($wpService->savedName);
    }

    public function testWriteReturnsFalseWhenChangesetMetaWriteFails(): void
    {
        $wpService = $this->createWpService(true, false);
        $config = $this->createConfig();
        $resolver = $this->createResolver(123);
        $writer = new CustomizeTokensWriter($wpService, $config, $resolver);

        $result = $writer->write($this->createMock(\WP_REST_Request::class), '{"a":1}');

        $this->assertFalse($result);
        $this->assertNull($wpService->revisionSavedForPostId);
    }

    public function testWriteFallsBackToThemeModWhenNoChangesetExists(): void
    {
        $wpService = $this->createWpService(true, true);
        $config = $this->createConfig();
        $resolver = $this->createResolver(null);
        $writer = new CustomizeTokensWriter($wpService, $config, $resolver);

        $result = $writer->write($this->createMock(\WP_REST_Request::class), '{"a":1}');

        $this->assertTrue($result);
        $this->assertSame('tokens', $wpService->savedName);
        $this->assertSame('{"a":1}', $wpService->savedValue);
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

    private function createWpService(bool $setThemeModResult, bool $updatePostMetaResult): SetThemeMod&UpdatePostMeta&WpSavePostRevision
    {
        return new class($setThemeModResult, $updatePostMetaResult) implements SetThemeMod, UpdatePostMeta, WpSavePostRevision {
            public ?string $savedName = null;
            public mixed $savedValue = null;
            public ?int $updatedPostId = null;
            public ?string $updatedMetaKey = null;
            public mixed $updatedMetaValue = null;
            public ?int $revisionSavedForPostId = null;

            public function __construct(
                private bool $setThemeModResult,
                private bool $updatePostMetaResult,
            ) {}

            public function setThemeMod(string $name, mixed $value): bool
            {
                $this->savedName = $name;
                $this->savedValue = $value;

                return $this->setThemeModResult;
            }

            public function updatePostMeta(int $postId, string $metaKey, mixed $metaValue, mixed $prevValue = ''): int|bool
            {
                $this->updatedPostId = $postId;
                $this->updatedMetaKey = $metaKey;
                $this->updatedMetaValue = $metaValue;

                return $this->updatePostMetaResult;
            }

            public function wpSavePostRevision(int $postId): mixed
            {
                $this->revisionSavedForPostId = $postId;
                return $postId;
            }
        };
    }
}
