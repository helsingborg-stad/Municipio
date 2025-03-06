<?php

namespace Municipio\ExternalContent\Cron\WpCronJobFromPostTypeSettings;

use Municipio\Config\Features\ExternalContent\ExternalContentPostTypeSettings\ExternalContentPostTypeSettingsInterface;
use Municipio\Config\Features\ExternalContent\SourceConfig\TypesenseSourceConfigInterface;
use Municipio\Config\Features\ExternalContent\SourceConfig\JsonSourceConfigInterface;
use Municipio\ExternalContent\Config\SourceConfigInterface;
use Municipio\ExternalContent\Filter\FilterDefinition\Contracts\FilterDefinition;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class WpCronJobFromPostTypeSettingsTest extends TestCase
{
    /**
     * @testdox Cron job uses post type as hook name.
     */
    public function testCreate()
    {
        $postTypeSetting = $this->getPostTypeSetting();
        $cronJob         = new WpCronJobFromPostTypeSettings($postTypeSetting, new FakeWpService());

        $this->assertEquals($postTypeSetting->getPostType(), $cronJob->getHookName());
    }

    /**
     * @testdox Cron job uses cron schedule as interval.
     */
    public function testInterval()
    {
        $postTypeSetting = $this->getPostTypeSetting();
        $cronJob         = new WpCronJobFromPostTypeSettings($postTypeSetting, new FakeWpService());

        $this->assertEquals($postTypeSetting->getAutomaticImportSchedule(), $cronJob->getSchedule());
    }

    /**
     * @testdox Cron job calls action with post type as argument.
     */
    public function testCallback()
    {
        $postTypeSetting = $this->getPostTypeSetting();
        $wpService       = new FakeWpService();
        $cronJob         = new WpCronJobFromPostTypeSettings($postTypeSetting, $wpService);

        $callback = $cronJob->getCallback();
        $callback();

        $this->assertCount(1, $wpService->methodCalls['doAction']);
        $this->assertEquals('Municipio/ExternalContent/Sync', $wpService->methodCalls['doAction'][0][0]);
        $this->assertEquals($postTypeSetting->getPostType(), $wpService->methodCalls['doAction'][0][1]);
    }

    private function getPostTypeSetting(): SourceConfigInterface
    {
        return new class implements SourceConfigInterface {
            public function getPostType(): string
            {
                return 'event';
            }
            public function getAutomaticImportSchedule(): string
            {
                return 'weekly';
            }
            public function getTaxonomies(): array
            {
                return [];
            }
            public function getSchemaType(): string
            {
                return 'event';
            }
            public function getSourceType(): string
            {
                return 'typesense';
            }
            public function getSourceJsonFilePath(): string
            {
                return '';
            }
            public function getSourceTypesenseApiKey(): string
            {
                return '';
            }
            public function getSourceTypesenseCollection(): string
            {
                return '';
            }
            public function getSourceTypesenseHost(): string
            {
                return '';
            }
            public function getSourceTypesensePort(): string
            {
                return '';
            }
            public function getSourceTypesenseProtocol(): string
            {
                return '';
            }
            public function getId(): string
            {
                return 'test-id';
            }
            public function getFilterDefinition(): FilterDefinition
            {
                return new FilterDefinition([]);
            }
        };
    }
}
