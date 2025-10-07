<?php

namespace Municipio\PostObject\Icon\Resolvers;

use AcfService\Implementations\FakeAcfService;
use Municipio\PostObject\PostObjectInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class PostIconResolverTest extends TestCase
{
    /**
     * @testdox class can be instantiated
     */
    public function testCanBeInstantiated()
    {
        $resolver = new PostIconResolver($this->getMockPostObject(), new FakeAcfService(), $this->getMockIconResolver());

        $this->assertInstanceOf(PostIconResolver::class, $resolver);
    }

    /**
     * @testdox calls inner resolver if no icon is found
     */
    public function testCallsInnerResolverIfNoIconIsFound()
    {
        $postObject    = $this->getMockPostObject();
        $acfService    = new FakeAcfService();
        $innerResolver = $this->getMockIconResolver();
        $innerResolver->expects($this->once())->method('resolve')->willReturn(null);
        $resolver = new PostIconResolver($postObject, $acfService, $innerResolver);

        $icon = $resolver->resolve();

        $this->assertNull($icon);
    }

    /**
     * @testdox returns icon if found
     */
    public function testReturnsIconIfFound()
    {
        $postObject = $this->getMockPostObject();
        $acfService = new FakeAcfService(['getField' => [
            'icon'  => [
                'type'          => 'icon',
                'material_icon' => 'anchor'
            ],
            'color' => '#8f32f2'
         ]]);

        $resolver = new PostIconResolver($postObject, $acfService, $this->getMockIconResolver());

        $this->assertSame('anchor', $resolver->resolve()->getIcon());
        $this->assertSame('#8f32f2', $resolver->resolve()->getCustomColor());
    }

    /**
     * @testdox returns svg icon if found
     */
    public function testReturnsSvgIconIfFound()
    {
        $postObject = $this->getMockPostObject();
        $acfService = new FakeAcfService(['getField' => [
            'icon'  => [
                'type' => 'svg',
                'svg'  => [
                    'url' => 'http://example.com/icon.svg'
                ]
            ],
            'color' => '#8f32f2'
        ]]);

        $resolver = new PostIconResolver($postObject, $acfService, $this->getMockIconResolver());

        $this->assertSame('http://example.com/icon.svg', $resolver->resolve()->getIcon());
        $this->assertSame('#8f32f2', $resolver->resolve()->getCustomColor());
    }

    /**
     * @testdox calls inner resolver if icon is invalid
     */
    public function testCallsInnerResolverIfIconIsInvalid()
    {
        $postObject = $this->getMockPostObject();
        $acfService = new FakeAcfService(['getField' => ['icon' => []]]);

        $innerResolver = $this->getMockIconResolver();
        $innerResolver->expects($this->once())->method('resolve')->willReturn(null);

        $resolver = new PostIconResolver($postObject, $acfService, $innerResolver);

        $icon = $resolver->resolve();

        $this->assertNull($icon);
    }


    private function getMockPostObject(): PostObjectInterface|MockObject
    {
        return $this->createMock(PostObjectInterface::class);
    }

    private function getMockIconResolver(): IconResolverInterface|MockObject
    {
        return $this->createMock(IconResolverInterface::class);
    }
}
