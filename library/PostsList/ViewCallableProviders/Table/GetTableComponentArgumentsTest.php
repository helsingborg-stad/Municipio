<?php

namespace Municipio\PostsList\ViewCallableProviders\Table;

use Municipio\PostsList\Config\AppearanceConfig\DefaultAppearanceConfig;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class GetTableComponentArgumentsTest extends TestCase
{
    #[TestDox('returns array with headings and list')]
    public function testGetTableArguments(): void
    {
        $getTableComponentArguments = new GetTableComponentArguments([], new DefaultAppearanceConfig(), new FakeWpService());
        $callable                   = $getTableComponentArguments->getCallable();
        $result                     = $callable();

        $this->assertIsArray($result);
        $this->isArray($result['headings']);
        $this->isArray($result['list']);
    }
}
