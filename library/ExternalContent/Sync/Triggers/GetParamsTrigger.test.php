<?php

namespace Municipio\ExternalContent\Sync\Triggers;

use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class GetParamsTriggerTest extends TestCase
{
    /**
     * @testdox runs inner trigger if no get params are set
     */
    public function testRunsInnerTriggerIfNoGetParamsAreSet()
    {
        $inner   = $this->getInner();
        $trigger = new GetParamsTrigger(new FakeWpService(), $inner);

        $trigger->trigger();

        $this->assertEquals(1, $inner->nbrOfCalls);
    }

    /**
     * @testdox does action if get params are set and user is in admin
     */
    public function testDoesActionIfGetParamsAreSetAndUserIsInAdmin()
    {
        $wpService = new FakeWpService([
            'isAdmin'   => true,
            'addAction' => function ($hook, $callback) {
                call_user_func($callback);
                return true;
            }]);

        $_GET[GetParamsTrigger::GET_PARAM_TRIGGER]   = true;
        $_GET[GetParamsTrigger::GET_PARAM_POST_TYPE] = 'test_post_type';

        $trigger = new GetParamsTrigger($wpService, $this->getInner());

        $trigger->trigger();

        $this->assertEquals('Municipio/ExternalContent/Sync', $wpService->methodCalls['doAction'][0][0]);
        $this->assertEquals('test_post_type', $wpService->methodCalls['doAction'][0][1]);
        $this->assertNull($wpService->methodCalls['doAction'][0][2]);
    }

    /**
     * @testdox does action with post id if get params are set and user is in admin
     */
    public function testDoesActionWithPostIdIfGetParamsAreSetAndUserIsInAdmin()
    {
        $wpService = new FakeWpService([
            'isAdmin'   => true,
            'addAction' => function ($hook, $callback) {
                call_user_func($callback);
                return true;
            }]);

        $_GET[GetParamsTrigger::GET_PARAM_TRIGGER]   = true;
        $_GET[GetParamsTrigger::GET_PARAM_POST_TYPE] = 'test_post_type';
        $_GET[GetParamsTrigger::GET_PARAM_POST_ID]   = 123;

        $trigger = new GetParamsTrigger($wpService, $this->getInner());

        $trigger->trigger();

        $this->assertEquals(123, $wpService->methodCalls['doAction'][0][2]);
    }

    private function getInner(): TriggerInterface
    {
        return new class implements TriggerInterface {
            public int $nbrOfCalls = 0;
            public function trigger(): void
            {
                $this->nbrOfCalls++;
            }
        };
    }
}
