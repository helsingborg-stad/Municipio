<?php

namespace Municipio\PostsList\ViewCallableProviders;

use Municipio\Helper\WpService;
use Municipio\PostObject\NullPostObject;
use Municipio\PostsList\Config\AppearanceConfig\DefaultAppearanceConfig;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class PostObjectWithContent extends NullPostObject
{
    public function __construct(private string $content)
    {
    }

    public function getContent(): string
    {
        return $this->content;
    }
}

class AppearanceConfigWithReadingTime extends DefaultAppearanceConfig
{
    public function __construct(private bool $shouldDisplayReadingTime)
    {
    }
    public function shouldDisplayReadingTime(): bool
    {
        return $this->shouldDisplayReadingTime;
    }
}

class GetReadingTimeTest extends TestCase
{
    #[TestDox('returns reading time when enabled')]
    public function testReturnsReadingTimeWhenEnabled(): void
    {
        $this->setupWpService();
        $appearanceConfig = new AppearanceConfigWithReadingTime(true);
        $getReadingTime   = new GetReadingTime($appearanceConfig);
        $post             = new PostObjectWithContent(str_repeat('word ', 400)); // 400 words
        $callable         = $getReadingTime->getCallable();

        $this->assertEquals('2 minutes', $callable($post));
    }

    #[TestDox('returns null when reading time is disabled')]
    public function testReturnsNullWhenReadingTimeIsDisabled(): void
    {
        $this->setupWpService();
        $appearanceConfig = new AppearanceConfigWithReadingTime(false);
        $getReadingTime   = new GetReadingTime($appearanceConfig);
        $post             = new PostObjectWithContent(str_repeat('word ', 400)); // 400 words

        $this->assertNull($getReadingTime->getCallable()($post));
    }

    private function setupWpService(): void
    {
        WpService::set(new FakeWpService([
            '__' => fn($text, $domain) => $text,
            '_n' => fn($single, $plural, $number, $domain) => $number === 1 ? $single : $plural,
        ]));
    }
}
