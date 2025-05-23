<?php

namespace Municipio\MirroredPost;

use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class MirroredPostFeatureTest extends TestCase
{
    /**
     * @testdox class can be instantiated
     */
    public function testClassCanBeInstantiated(): void
    {
        $mirroredPostFeature = new MirroredPostFeature($this->getWpService());

        $this->assertInstanceOf(MirroredPostFeature::class, $mirroredPostFeature);
    }

    /**
     * @testdox enable method does not throw an exception
     */
    public function testEnableMethodDoesNotThrowException(): void
    {
        $mirroredPostFeature = new MirroredPostFeature($this->getWpService());

        try {
            $mirroredPostFeature->enable();
            $this->assertTrue(true, 'Enable method executed without exceptions.');
        } catch (\Exception $e) {
            $this->fail('Enable method threw an exception: ' . $e->getMessage());
        }
    }

    private function getWpService(): FakeWpService
    {
        return new FakeWpService([ 'addFilter' => true, 'addAction' => true ]);
    }
}
