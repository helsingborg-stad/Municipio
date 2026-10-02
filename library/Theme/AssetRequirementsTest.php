<?php

declare(strict_types=1);

namespace Municipio\Theme;

use PHPUnit\Framework\TestCase;
use WpUtilService\Features\Enqueue\EnqueueManagerInterface;

class AssetRequirementsTest extends TestCase
{
    protected function tearDown(): void
    {
        AssetRequirements::setInstance(null);
    }

    public function testRequirementsMapToAssetsOnlyOnce(): void
    {
        $assets = [];
        $enqueue = $this->createMock(EnqueueManagerInterface::class);
        $enqueue->method('add')->willReturnCallback(
            static function (string $asset, array $dependencies = []) use (&$assets, $enqueue): EnqueueManagerInterface {
                $assets[] = [$asset, $dependencies];
                return $enqueue;
            },
        );

        $requirements = new AssetRequirements($enqueue);
        $requirements->add('shell');
        $requirements->add('comments');
        $requirements->add('posts-list');
        $requirements->add('comments');

        self::assertSame([
            ['js/municipio-shell.js', ['jquery', 'wp-api-fetch']],
            ['css/municipio.css', []],
            ['js/municipio-comments.js', []],
            ['css/municipio-comments.css', []],
            ['js/municipio-posts-list.js', ['wp-api-fetch']],
        ], $assets);
    }

    public function testUnknownRequirementIsRejected(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        (new AssetRequirements($this->createMock(EnqueueManagerInterface::class)))->add('unknown');
    }
}
