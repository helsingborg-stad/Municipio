<?php

namespace Municipio\Chat;

use AcfService\Implementations\FakeAcfService;
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
        );

        $this->assertInstanceOf(ChatFeature::class, $feature);
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
}
