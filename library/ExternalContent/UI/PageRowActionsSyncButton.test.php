<?php

namespace Municipio\ExternalContent\UI;

use Municipio\TestUtils\WpMockFactory;
use PHPUnit\Framework\TestCase;

class PageRowActionsSyncButtonTest extends TestCase
{
    /**
     * @testdox Class exists
     */
    public function testClassExists()
    {
        $this->assertTrue(class_exists(\Municipio\ExternalContent\UI\PageRowActionsSyncButton::class));
    }

    /**
     * @testdox addSyncButton returns an array
     */
    public function testAddSyncButtonReturnsAnArray()
    {
        $pageRowActionsSyncButton = new \Municipio\ExternalContent\UI\PageRowActionsSyncButton([], new \WpService\Implementations\FakeWpService());
        $this->assertIsArray($pageRowActionsSyncButton->addSyncButton([], WpMockFactory::createWpPost()));
    }

    /**
     * @testdox filters are added for both page_row_actions and post_row_actions
     */
    public function testFiltersAreAddedForBothPageRowActionsAndPostRowActions()
    {
        $wpService                = new \WpService\Implementations\FakeWpService(['currentUserCan' => true]);
        $pageRowActionsSyncButton = new \Municipio\ExternalContent\UI\PageRowActionsSyncButton([], $wpService);

        $pageRowActionsSyncButton->addHooks();

        $this->assertEquals('page_row_actions', $wpService->methodCalls['addFilter'][0][0]);
        $this->assertEquals('post_row_actions', $wpService->methodCalls['addFilter'][1][0]);
    }
}
