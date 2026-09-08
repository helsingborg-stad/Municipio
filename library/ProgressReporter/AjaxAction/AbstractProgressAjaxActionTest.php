<?php

declare(strict_types=1);

namespace Municipio\ProgressReporter\AjaxAction;

use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

/**
 * Tests the shared progress AJAX request flow.
 */
class AbstractProgressAjaxActionTest extends TestCase
{
    /**
     * Use the required HTTP method unless a test overrides it.
     */
    protected function setUp(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
    }

    /**
     * Remove request state after each test.
     */
    protected function tearDown(): void
    {
        unset($_SERVER['REQUEST_METHOD']);
    }

    /**
     * Verify the action registers its authenticated AJAX hook.
     */
    public function testRegistersAjaxHook(): void
    {
        $hooks = [];
        $action = $this->createAction(new FakeWpService([
            'addAction' => static function (string $hook, callable $callback) use (&$hooks): true {
                $hooks[$hook] = $callback;
                return true;
            },
        ]));

        $action->addHooks();

        static::assertArrayHasKey('wp_ajax_test_progress_action', $hooks);
    }

    /**
     * Verify unauthorized requests stop before execution.
     */
    public function testRejectsUnauthorizedRequest(): void
    {
        [$action, $reporter] = $this->createRecordingAction(['currentUserCan' => false]);

        $action->handleRequest();

        static::assertSame(['Unauthorized'], $reporter->messages);
        static::assertSame(0, $action->executionCount);
    }

    /**
     * Verify requests using the wrong HTTP method stop before execution.
     */
    public function testRejectsInvalidMethod(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        [$action, $reporter] = $this->createRecordingAction(['currentUserCan' => true]);

        $action->handleRequest();

        static::assertSame(['Invalid method'], $reporter->messages);
        static::assertSame(0, $action->executionCount);
    }

    /**
     * Verify invalid nonces stop before execution.
     */
    public function testRejectsInvalidNonce(): void
    {
        [$action, $reporter] = $this->createRecordingAction([
            'currentUserCan' => true,
            'checkAjaxReferer' => false,
        ]);

        $action->handleRequest();

        static::assertSame(['Invalid nonce'], $reporter->messages);
        static::assertSame(0, $action->executionCount);
    }

    /**
     * Verify valid requests execute and report completion.
     */
    public function testExecutesValidRequest(): void
    {
        [$action, $reporter] = $this->createRecordingAction([
            'currentUserCan' => true,
            'checkAjaxReferer' => 1,
        ]);

        $action->handleRequest();

        static::assertSame(['Complete'], $reporter->messages);
        static::assertSame(1, $action->executionCount);
    }

    /**
     * Verify operation failures are converted to safe messages.
     */
    public function testReportsOperationFailure(): void
    {
        [$action, $reporter] = $this->createRecordingAction([
            'currentUserCan' => true,
            'checkAjaxReferer' => 1,
        ]);
        $action->shouldFail = true;

        $action->handleRequest();

        static::assertSame(['Failed'], $reporter->messages);
    }

    /**
     * Create a configurable test action.
     *
     * @param array<string, mixed> $wpServiceMethods
    * @return array{0: TestProgressAjaxAction, 1: RecordingProgressReporter}
     */
    private function createRecordingAction(array $wpServiceMethods): array
    {
        $reporter = new RecordingProgressReporter();
        $action = $this->createAction(new FakeWpService($wpServiceMethods), $reporter);

        return [$action, $reporter];
    }

    /**
     * Create the concrete test action.
     */
    private function createAction(
        FakeWpService $wpService,
        ?RecordingProgressReporter $progressReporter = null,
    ): TestProgressAjaxAction {
        return new TestProgressAjaxAction(
            $wpService,
            $progressReporter ?? new RecordingProgressReporter(),
            new ProgressAjaxActionConfig(
                action: 'test_progress_action',
                requiredCapability: 'manage_options',
                messages: new ProgressAjaxActionMessages('Unauthorized', 'Invalid method', 'Invalid nonce'),
                requiredMethod: 'POST',
                nonceAction: 'test_progress_action',
            ),
        );
    }
}