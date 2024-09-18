<?php

namespace Municipio\ExternalContent\Sync\Triggers;

use PHPUnit\Framework\TestCase;
use WpService\Contracts\DoAction;
use WpService\Implementations\FakeWpService;

class TriggerSyncTest extends TestCase
{
    /**
     * @testdox calls action with exact hook name, post type, post id if post id is set
     */
    public function testCallsActionWithPostIdIfPostIdIsSet()
    {
        $wpService = new FakeWpService();
        $trigger   = $this->getTriggerInstance('test_post_type', 123, $wpService);

        $trigger->callProtectedTriggerMethod();

        $this->assertEquals('Municipio/ExternalContent/Sync', $wpService->methodCalls['doAction'][0][0]);
        $this->assertEquals('test_post_type', $wpService->methodCalls['doAction'][0][1]);
        $this->assertEquals(123, $wpService->methodCalls['doAction'][0][2]);
    }

    private function getTriggerInstance(string $postType, ?int $postId, DoAction $wpService)
    {
        return new class ($postType, $postId, $wpService) extends TriggerSync {
            public function __construct(
                private string $postType,
                private ?int $postId,
                DoAction $wpService
            ) {
                parent::__construct($wpService);
            }
            public function callProtectedTriggerMethod(): void
            {
                $this->trigger($this->postType, $this->postId);
            }
        };
    }
}
