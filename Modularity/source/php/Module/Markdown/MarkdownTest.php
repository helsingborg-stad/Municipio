<?php

namespace Modularity\Module\Markdown;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class MarkdownTest extends TestCase
{
    protected function setUp(): void
    {
        if (!defined('MINUTE_IN_SECONDS')) {
            define('MINUTE_IN_SECONDS', 60);
        }
    }

    #[TestDox('class can be instantiated')]
    public function testClassCanBeInstantiated()
    {
        $markdown = new Markdown();
        $this->assertInstanceOf(Markdown::class, $markdown);
    }
}
