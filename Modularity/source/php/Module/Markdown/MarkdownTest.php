<?php

namespace Modularity\Module\Markdown;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use Modularity\Helper\AcfService;
use Modularity\Helper\WpService;

class MarkdownTest extends TestCase{ 

    protected function setUp(): void {
        WpService::set(new \WpService\Implementations\FakeWpService(['addAction' => true]));   

        if(!defined('MINUTE_IN_SECONDS')) {
            define('MINUTE_IN_SECONDS', 60);
        }
    }

    #[TestDox('class can be instantiated')]
    public function testClassCanBeInstantiated() {
        $markdown = new Markdown();
        $this->assertInstanceOf(Markdown::class, $markdown);
    }
}