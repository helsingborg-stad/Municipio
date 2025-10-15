<?php

namespace Modularity\Helper;

use AcfService\Implementations\FakeAcfService;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class AcfServiceTest extends TestCase {
        
    #[TestDox('::get throws an exception when AcfService is not set')]
    #[RunInSeparateProcess]
    public function testGetThrowsAnExceptionWhenAcfServiceIsNotSet() {
        $this->expectException(\RuntimeException::class);
        \Modularity\Helper\AcfService::get();
    }

    #[TestDox('::set sets the AcfService instance')]
    public function testSetSetsTheAcfServiceInstance() {
        \Modularity\Helper\AcfService::set(new FakeAcfService());
        $this->assertInstanceOf(\AcfService\AcfService::class, \Modularity\Helper\AcfService::get());
    }
}