<?php

declare(strict_types=1);

namespace Modularity\Module\Text;

use AcfService\AcfService;
use AcfService\Implementations\FakeAcfService;
use Modularity\Helper\AcfService as HelperAcfService;
use Modularity\Helper\WpService as HelperWpService;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;
use WpService\WpService;

class TextTest extends TestCase
{
    #[TestDox('sets postContent to empty string when no content is provided')]
    public function testEmptyPostContent(): void
    {
        HelperWpService::set(self::createWpService());
        HelperAcfService::set(self::createAcfService());

        $textModule = new Text();
        $textModule->data = [];

        $data = $textModule->data();

        static::assertSame('', $data['postContent']);
    }

    #[TestDox('sets postContent to the value of post_content field when provided')]
    public function testPostContentFromPostContentField(): void
    {
        HelperWpService::set(self::createWpService());
        HelperAcfService::set(self::createAcfService(['post_content' => 'Test content']));

        $textModule = new Text();

        $data = $textModule->data();

        static::assertSame('Test content', $data['postContent']);
    }

    #[TestDox('sets postContent to the value of content field when post_content is not provided')]
    public function testPostContentFromContentField(): void
    {
        HelperWpService::set(self::createWpService());
        HelperAcfService::set(self::createAcfService(['content' => 'Fallback content']));

        $textModule = new Text();

        $data = $textModule->data();

        static::assertSame('Fallback content', $data['postContent']);
    }

    private static function createWpService(): WpService
    {
        return new class extends FakeWpService {
            public function addAction(string $hook, callable $callback, int $priority = 10, int $acceptedArgs = 1): true
            {
                return true;
            }

            public function applyFilters(string $tag, mixed $value, mixed ...$args): mixed
            {
                return $value;
            }
        };
    }

    private static function createAcfService(array $fields = []): AcfService
    {
        return new class($fields) extends FakeAcfService {
            private array $fields;

            public function __construct(array $fields)
            {
                $this->fields = $fields;
            }

            public function getFields(mixed $postId = false, bool $formatValue = true, bool $escapeHtml = false): array|false
            {
                return $this->fields;
            }
        };
    }
}
