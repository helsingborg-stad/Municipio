<?php

namespace Municipio\ExternalContent\SyncHandler\Cleanup;

use Municipio\ExternalContent\Config\SourceConfigInterface;
use Municipio\ExternalContent\Config\SourceTaxonomyConfigInterface;
use Municipio\ExternalContent\SyncHandler\SyncHandler;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class CleanupTermsNoLongerInUseTest extends TestCase
{
    /**
     * @testdox class can be instantiated
     */
    public function testClassCanBeInstantiated()
    {
        $cleanup = new CleanupTermsNoLongerInUse($this->createSourceConfigMock(), new FakeWpService());
        $this->assertInstanceOf(CleanupTermsNoLongerInUse::class, $cleanup);
    }

    /**
     * @testdox addHook adds a hook for the cleanup method
     */
    public function testAddHookAddsHookForCleanupMethod()
    {
        $wpService = new FakeWpService(['addAction' => true]);
        $cleanup   = new CleanupTermsNoLongerInUse($this->createSourceConfigMock(), $wpService);

        $cleanup->addHooks();

        $this->assertEquals(SyncHandler::ACTION_AFTER, $wpService->methodCalls['addAction'][0][0]);
        $this->assertEquals([$cleanup, 'cleanup'], $wpService->methodCalls['addAction'][0][1]);
    }

    /**
     * @testdox does not call wpDeleteTerm if no taxonomies are configured
     */
    public function testDoesNotCallWpDeleteTermIfNoTaxonomiesConfigured()
    {
        $wpService = new FakeWpService();
        $cleanup   = new CleanupTermsNoLongerInUse($this->createSourceConfigMock(), $wpService);

        $cleanup->cleanup();

        $this->assertArrayNotHasKey('wpDeleteTerm', $wpService->methodCalls);
    }

    /**
     * @testdox does not call wpDeleteTerm if no terms are found
     */
    public function testDoesNotCallWpDeleteTermIfNoTermsFound()
    {
        $sourceConfig = $this->createSourceConfigMock([$this->createTaxonomyConfigMock('category')]);
        $wpService    = new FakeWpService(['getTerms' => []]);
        $cleanup      = new CleanupTermsNoLongerInUse($sourceConfig, $wpService);

        $cleanup->cleanup();

        $this->assertArrayNotHasKey('wpDeleteTerm', $wpService->methodCalls);
    }

    /**
     * @testdox calls wpDeleteTerm for each term that is no longer in use
     */
    public function testCallsWpDeleteTermForEachTermNoLongerInUse()
    {
        $sourceConfig     = $this->createSourceConfigMock([$this->createTaxonomyConfigMock('category')]);
        $termsToBeDeleted = [(object)['term_id' => 1, 'taxonomy' => 'category', 'count' => 0]];
        $wpService        = new FakeWpService(['getTerms' => $termsToBeDeleted, 'wpDeleteTerm' => true]);
        $cleanup          = new CleanupTermsNoLongerInUse($sourceConfig, $wpService);

        $cleanup->cleanup();

        $this->assertCount(1, $wpService->methodCalls['wpDeleteTerm']);
        $this->assertEquals(1, $wpService->methodCalls['wpDeleteTerm'][0][0]);
        $this->assertEquals('category', $wpService->methodCalls['wpDeleteTerm'][0][1]);
    }

    /**
     * @testdox does not call wpDeleteTerm if no terms are no longer in use
     */
    public function testDoesNotCallWpDeleteTermIfNoTermsNoLongerInUse()
    {
        $sourceConfig = $this->createSourceConfigMock([$this->createTaxonomyConfigMock('category')]);
        $terms        = [(object)['term_id' => 1, 'taxonomy' => 'category', 'count' => 1]];
        $wpService    = new FakeWpService(['getTerms' => $terms]);
        $cleanup      = new CleanupTermsNoLongerInUse($sourceConfig, $wpService);

        $cleanup->cleanup();

        $this->assertArrayNotHasKey('wpDeleteTerm', $wpService->methodCalls);
    }

    private function createSourceConfigMock(array $taxonomies = []): SourceConfigInterface|MockObject
    {
        $sourceConfig = $this->createMock(SourceConfigInterface::class);
        $sourceConfig->method('getTaxonomies')->willReturn($taxonomies);
        return $sourceConfig;
    }

    private function createTaxonomyConfigMock(string $name): SourceTaxonomyConfigInterface|MockObject
    {
        $taxonomyConfig = $this->createMock(SourceTaxonomyConfigInterface::class);
        $taxonomyConfig->method('getName')->willReturn($name);
        return $taxonomyConfig;
    }
}
