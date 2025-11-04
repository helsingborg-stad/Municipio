<?php

namespace Municipio\PostsList\ViewUtilities\Table;

use Municipio\PostObject\PostObjectInterface;
use Municipio\PostsList\Config\AppearanceConfig\AppearanceConfigInterface;
use Municipio\PostsList\Config\AppearanceConfig\DefaultAppearanceConfig;
use Municipio\PostsList\ViewUtilities\ViewUtilityInterface;
use Municipio\PostsList\ViewUtilities\Table\TableArguments\{TableHeadingsGenerator, TableItemsGenerator};
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;
use WpService\WpService;

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
