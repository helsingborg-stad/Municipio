<?php

namespace Municipio\Chat;

use AcfService\Implementations\FakeAcfService;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;
use WpUtilService\Features\Enqueue\EnqueueManager;

class ChatFeatureTest extends TestCase
{
    #[TestDox('class can be instantiated')]
    public function testClassCanBeInstantiated(): void
    {
        $wpService = new FakeWpService([
            'addAction' => true,
        ]);
        $acfService = new FakeAcfService([
            'getField' => fn() => false,
        ]);
        $enqueue = new EnqueueManager($wpService);
        $this->assertInstanceOf(ChatFeature::class, new ChatFeature($wpService, $acfService, $enqueue));
    }

    #[TestDox('enable() can be called')]
    public function testEnableCanBeCalled(): void
    {
        $wpService = new FakeWpService([
            'addAction' => true,
        ]);
        $acfService = new FakeAcfService([
            'getField' => fn() => false,
        ]);
        $enqueue = new EnqueueManager($wpService);
        $feature = new ChatFeature($wpService, $acfService, $enqueue);
        $this->assertNull($feature->enable());
    }
}
