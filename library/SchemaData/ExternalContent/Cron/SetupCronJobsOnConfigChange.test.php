<?php

namespace Municipio\SchemaData\ExternalContent\Cron;

use Municipio\SchemaData\ExternalContent\Config\SourceConfigInterface;
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
        $wpService = new FakeWpService(['addAction' => true]);
        $sut       = new SetupCronJobsOnConfigChange([$this->getFakeConfig()], new FakeWpCronJobManager(), $wpService);

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
        $sut       = new SetupCronJobsOnConfigChange([$this->getFakeConfig()], $manager, $wpService);

        $sut->setupCronJobs('options', 'schema-data');

        $this->assertCount(1, $manager->methodCalls['register']);
    }

    private function getFakeConfig(): SourceConfigInterface|MockObject
    {
        $postType         = 'test_post_type';
        $cronSchedule     = 'hourly';
        $fakeSourceConfig = $this->getMockBuilder(SourceConfigInterface::class)->getMock();
        $fakeSourceConfig->method('getPostType')->willReturn($postType);
        $fakeSourceConfig->method('getAutomaticImportSchedule')->willReturn($cronSchedule);

        return $fakeSourceConfig;
    }
}
