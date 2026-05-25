<?php

declare(strict_types=1);

namespace Municipio\Chat\Module;

use AcfService\Implementations\FakeAcfService;
use Modularity\Helper\AcfService;
use Modularity\Helper\WpService;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class ChatModuleTest extends TestCase
{
    protected function setUp(): void
    {
        WpService::set(new FakeWpService(['addAction' => true]));
        AcfService::set(new FakeAcfService(['getFields' => []]));

        if (!defined('MINUTE_IN_SECONDS')) {
            define('MINUTE_IN_SECONDS', 60);
        }
    }

    #[TestDox('class can be instantiated')]
    public function testClassCanBeInstantiated(): void
    {
        $module = new ChatModule();

        $this->assertInstanceOf(ChatModule::class, $module);
    }

    #[TestDox('slug is set to chat')]
    public function testSlugIsChat(): void
    {
        $module = new ChatModule();

        $this->assertSame('chat', $module->slug);
    }

    #[TestDox('template() returns chat.blade.php')]
    public function testTemplateReturnsChatBladePhp(): void
    {
        $module = new ChatModule();

        $this->assertSame('chat.blade.php', $module->template());
    }

    #[TestDox('data() returns an array')]
    public function testDataReturnsArray(): void
    {
        $module = new ChatModule();

        $this->assertIsArray($module->data());
    }
}
