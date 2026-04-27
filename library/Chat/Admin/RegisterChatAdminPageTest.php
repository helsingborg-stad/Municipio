<?php

namespace Municipio\Chat\Admin;

use AcfService\Implementations\FakeAcfService;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class RegisterChatAdminPageTest extends TestCase
{
    #[TestDox('addHooks() registers the init action')]
    public function testAddHooksRegistersInitAction(): void
    {
        $wpService = $this->getWpService();

        (new RegisterChatAdminPage($wpService, $this->getAcfService()))->addHooks();

        $this->assertCount(1, $wpService->methodCalls['addAction'] ?? []);
        $this->assertSame('init', $wpService->methodCalls['addAction'][0][0]);
    }

    #[TestDox('register() registers an ACF options page')]
    public function testRegisterCallsAcfServiceAddOptionsPage(): void
    {
        $acfService = $this->getAcfService();

        (new RegisterChatAdminPage($this->getWpService(), $acfService))->register();

        $this->assertArrayHasKey('addOptionsPage', $acfService->methodCalls);
        $this->assertCount(1, $acfService->methodCalls['addOptionsPage']);
    }

    private function getWpService(): FakeWpService
    {
        return new FakeWpService([
            'addAction' => true,
            '__' => '',
        ]);
    }

    private function getAcfService(): FakeAcfService
    {
        return new FakeAcfService([
            'getField' => null,
        ]);
    }
}
