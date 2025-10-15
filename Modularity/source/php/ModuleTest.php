<?php

namespace Modularity;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use Modularity\Helper\AcfService;
use Modularity\Helper\WpService;

class ModuleTest extends TestCase {
    protected function setUp(): void {
        WpService::set(new \WpService\Implementations\FakeWpService(['addAction' => true]));
        AcfService::set(new \AcfService\Implementations\FakeAcfService());
    }

    #[TestDox('class can be instantiated')]
    public function testClassCanBeInstantiated() {
        $module = new Module();
        $this->assertInstanceOf(Module::class, $module);
    }
}