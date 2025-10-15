<?php

namespace Modularity\Helper;

use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class WpServiceTest extends TestCase {
        
    #[TestDox('::get throws an exception when WpService is not set')]
    #[RunInSeparateProcess]
    public function testGetThrowsAnExceptionWhenWpServiceIsNotSet() {
        $this->expectException(\RuntimeException::class);
        \Modularity\Helper\WpService::get();
    }

    #[TestDox('::set sets the WpService instance')]
    public function testSetSetsTheWpServiceInstance() {
        \Modularity\Helper\WpService::set(new FakeWpService());
        $this->assertInstanceOf(\WpService\WpService::class, \Modularity\Helper\WpService::get());
    }
}