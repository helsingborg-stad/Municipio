<?php

declare(strict_types=1);

namespace Municipio\A11yStatement;

use AcfService\Implementations\FakeAcfService;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class A11yStatementTest extends TestCase
{
    #[TestDox('It registers the statement route after inherited common field values are available.')]
    public function testAddHooksRegistersFrontendPageAfterCommonFieldGroups(): void
    {
        $wpService = new FakeWpService([
            'addAction' => true,
            'addFilter' => true,
        ]);
        $instance = new A11yStatement($wpService, new FakeAcfService());

        $instance->addHooks();

        $registeredActions = $wpService->methodCalls['addAction'];
        $frontendPageAction = array_values(array_filter(
            $registeredActions,
            static fn(array $action): bool => $action[1] === [$instance, 'registerFrontendPage'],
        ));

        static::assertCount(1, $frontendPageAction);
        static::assertSame('init', $frontendPageAction[0][0]);
        static::assertSame(20, $frontendPageAction[0][2]);
    }
}
