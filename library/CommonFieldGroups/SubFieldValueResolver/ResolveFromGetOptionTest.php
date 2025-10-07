<?php

namespace Municipio\CommonFieldGroups\SubFieldValueResolver;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use WpService\Contracts\GetOption;

class ResolveFromGetOptionTest extends TestCase
{
    #[TestDox('class can be instantiated')]
    public function testCanInstantiateResolveFromGetOption(): void
    {
        $resolver = new ResolveFromGetOption($this->createGetOptionMock());
        $this->assertInstanceOf(ResolveFromGetOption::class, $resolver);
    }

    #[TestDox('resolver returns value from getOption()')]
    public function testResolveReturnsValueFromGetOption(): void
    {
        $wpServiceMock = $this->createGetOptionMock();
        $wpServiceMock->method('getOption')->willReturn('mocked value');

        $resolver = new ResolveFromGetOption($wpServiceMock);
        $result   = $resolver->resolve([], 'test_key');

        $this->assertSame('mocked value', $result);
    }

    private function createGetOptionMock(): GetOption|MockObject
    {
        return $this->createMock(GetOption::class);
    }
}
