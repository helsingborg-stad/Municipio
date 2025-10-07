<?php

namespace Municipio\MirroredPost;

use Municipio\MirroredPost\Utils\GetOtherBlogId\GetOtherBlogIdInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class MirroredPostFeatureTest extends TestCase
{
    #[TestDox('class can be instantiated')]
    public function testClassCanBeInstantiated(): void
    {
        $this->assertInstanceOf(MirroredPostFeature::class, $this->getInstance());
    }

    #[TestDox('enable method does not throw an exception')]
    public function testEnableMethodDoesNotThrowException(): void
    {
        $mirroredPostFeature = $this->getInstance();

        try {
            $mirroredPostFeature->enable();
            $this->assertTrue(true, 'Enable method executed without exceptions.');
        } catch (\Exception $e) {
            $this->fail('Enable method threw an exception: ' . $e->getMessage());
        }
    }

    private function getInstance(): MirroredPostFeature
    {
        return new MirroredPostFeature($this->getBlogIdGetter(), $this->getWpService());
    }

    private function getWpService(): FakeWpService
    {
        return new FakeWpService([ 'addFilter' => true, 'addAction' => true ]);
    }

    private function getBlogIdGetter(): GetOtherBlogIdInterface|MockObject
    {
        return $this->createMock(GetOtherBlogIdInterface::class);
    }
}
