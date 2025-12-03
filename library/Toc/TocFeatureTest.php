<?php

namespace Municipio\Toc;

use AcfService\Implementations\FakeAcfService;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class TocFeatureTest extends TestCase
{
    #[TestDox('class can be instantiated')]
    public function testClassCanBeInstantiated(): void
    {
        $tocFeature = new TocFeature($this->getWpService(), $this->getAcfService());

        $this->assertInstanceOf(TocFeature::class, $tocFeature);
    }

    #[TestDox('enable method does not throw an exception')]
    public function testEnableMethodDoesNotThrowException(): void
    {
        $tocFeature = new TocFeature($this->getWpService(), $this->getAcfService());

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

    private function getAcfService(): FakeAcfService
    {
        return new FakeAcfService([
            'getField'         => true,
            'getFields'        => true,
            'getFieldGroups'   => true,
            'getFieldGroup'    => true,
            'updateFieldGroup' => true,
            'deleteFieldGroup' => true,
            'addAction'        => true,
            'addFilter'        => true,
        ]);
    }
}
