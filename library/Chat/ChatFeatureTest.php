<?php

namespace Municipio\Chat;

use AcfService\Implementations\FakeAcfService;
use Municipio\Chat\Admin\RegisterChatAdminPage;
use Municipio\Chat\Api\RegisterChatEndpoint;
use Municipio\Chat\Frontend\EnqueueChatScripts;
use Municipio\Chat\Frontend\RenderGlobalChatBubble;
use Municipio\Chat\Module\RegisterChatModule;
use Municipio\HooksRegistrar\Hookable;
use Municipio\HooksRegistrar\HooksRegistrarInterface;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;
use WpUtilService\Features\Enqueue\EnqueueManagerInterface;

class ChatFeatureTest extends TestCase
{
    #[TestDox('class can be instantiated')]
    public function testClassCanBeInstantiated(): void
    {
        $feature = new ChatFeature(
            $this->getWpService(),
            $this->getAcfService(),
            $this->getEnqueueManager(),
            $this->getHooksRegistrar(),
        );

        $this->assertInstanceOf(ChatFeature::class, $feature);
    }

    #[TestDox('enable() registers the expected sub-hookables with the registrar')]
    public function testEnableRegistersAllHookables(): void
    {
        $registrar = $this->getHooksRegistrar();

        (new ChatFeature(
            $this->getWpService(),
            $this->getAcfService(),
            $this->getEnqueueManager(),
            $registrar,
        ))->enable();

        $registered = array_map('get_class', $registrar->registered);

        $this->assertContains(RegisterChatAdminPage::class, $registered);
        $this->assertContains(EnqueueChatScripts::class, $registered);
        $this->assertContains(RenderGlobalChatBubble::class, $registered);
        $this->assertContains(RegisterChatModule::class, $registered);
        $this->assertContains(RegisterChatEndpoint::class, $registered);
    }

    private function getWpService(): FakeWpService
    {
        return new FakeWpService([
            'addAction' => true,
            'addFilter' => true,
            'applyFilters' => fn($tag, $value) => $value,
            'wpCacheGet' => false,
            'wpCacheSet' => true,
            '__' => '',
        ]);
    }

    private function getAcfService(): FakeAcfService
    {
        return new FakeAcfService([
            'getField' => null,
        ]);
    }

    private function getEnqueueManager(): EnqueueManagerInterface
    {
        return $this->createMock(EnqueueManagerInterface::class);
    }

    private function getHooksRegistrar(): HooksRegistrarInterface
    {
        return new class implements HooksRegistrarInterface {
            /** @var Hookable[] */
            public array $registered = [];

            public function register(Hookable $object): HooksRegistrarInterface
            {
                $this->registered[] = $object;
                return $this;
            }
        };
    }
}
