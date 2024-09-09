<?php

namespace Municipio\ExternalContent\Cron;

use Municipio\Config\ConfigFactoryInterface;
use Municipio\Config\Features\ExternalContent\ExternalContentConfigInterface;
use Municipio\Config\Features\ExternalContent\ExternalContentPostTypeSettings\ExternalContentPostTypeSettingsInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use WpCronService\FakeWpCronJobManager;
use WpService\Implementations\FakeWpService;

class SetupCronJobsOnConfigChangeTest extends TestCase
{
    /**
     * @testdox Calls setupCronJobs on config change
     */
    public function testAddHooks()
    {
        $wpService = new FakeWpService();
        $sut       = new SetupCronJobsOnConfigChange($this->getFakeConfig(), new FakeWpCronJobManager(), $wpService);

        $sut->addHooks();

        $this->assertEquals('init', $wpService->methodCalls['addAction'][0][0]);
        $this->assertEquals([$sut, 'setupCronJobs'], $wpService->methodCalls['addAction'][0][1]);
    }

    /**
     * @testdox Converts post type settings from config to cron jobs
     */
    public function testSetupCronJobs()
    {
        $wpService = new FakeWpService();
        $manager   = new FakeWpCronJobManager();
        $sut       = new SetupCronJobsOnConfigChange($this->getFakeConfig(), $manager, $wpService);

        $sut->setupCronJobs('options', 'schema-data');

        $this->assertCount(1, $manager->methodCalls['register']);
    }

    private function getFakeConfig(): ExternalContentConfigInterface|MockObject
    {
        $postType            = 'test_post_type';
        $cronSchedule        = 'hourly';
        $fakePostTypeSetting = $this->getMockBuilder(ExternalContentPostTypeSettingsInterface::class)->getMock();
        $fakePostTypeSetting->method('getPostType')->willReturn($postType);
        $fakePostTypeSetting->method('getCronSchedule')->willReturn($cronSchedule);

        $fake = $this->getMockBuilder(ExternalContentConfigInterface::class)->getMock();
        $fake->method('getEnabledPostTypes')->willReturn([$postType]);
        $fake->method('getPostTypeSettings')->with($postType)->willReturn($fakePostTypeSetting);

        return $fake;
    }
}
