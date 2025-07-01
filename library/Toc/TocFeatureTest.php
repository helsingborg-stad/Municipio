<?php

namespace Municipio\Toc;

use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class TocFeatureTest extends TestCase
{
    /**
     * @testdox class can be instantiated
     */
    public function testClassCanBeInstantiated(): void
    {
        $tocFeature = new TocFeature($this->getWpService());

        $this->assertInstanceOf(TocFeature::class, $tocFeature);
    }

    /**
     * @testdox enable method does not throw an exception
     */
    public function testEnableMethodDoesNotThrowException(): void
    {
        $tocFeature = new TocFeature($this->getWpService());

        try {
            $tocFeature->enable();
            $this->assertTrue(true, 'Enable method executed without exceptions.');
        } catch (\Exception $e) {
            $this->fail('Enable method threw an exception: ' . $e->getMessage());
        }
    }

    private function getWpService(): FakeWpService
    {
        return new FakeWpService(['addFilter' => true, 'addAction' => true, 'isSingular' => true]);
    }
}