<?php

namespace Municipio\MirroredPost;

use Municipio\MirroredPost\Contracts\BlogIdQueryVar;
use Municipio\MirroredPost\PostObject\MirroredPostObject;
use Municipio\PostObject\Factory\CreatePostObjectFromWpPost;
use Municipio\PostObject\PostObjectInterface;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;
use WpService\WpService;

class MirroredPostFeatureTest extends TestCase
{
    /**
     * @testdox class can be instantiated
     */
    public function testClassCanBeInstantiated(): void
    {
        $mirroredPostFeature = new MirroredPostFeature(new FakeWpService());

        $this->assertInstanceOf(MirroredPostFeature::class, $mirroredPostFeature);
    }

    /**
     * @testdox enable method does not throw an exception
     */
    public function testEnableMethodDoesNotThrowException(): void
    {
        $mirroredPostFeature = new MirroredPostFeature(new FakeWpService());

        try {
            $mirroredPostFeature->enable();
            $this->assertTrue(true, 'Enable method executed without exceptions.');
        } catch (\Exception $e) {
            $this->fail('Enable method threw an exception: ' . $e->getMessage());
        }
    }
}
