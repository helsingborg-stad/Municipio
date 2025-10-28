<?php

namespace Modularity\Module\Archive;

use Modularity\Helper\WpService;
use PHPUnit\Framework\Attributes\TestDox;
use WpService\Implementations\FakeWpService;

class ArchiveTest extends \PHPUnit\Framework\TestCase
{
    protected function setUp(): void
    {
        WpService::set(new FakeWpService([
            'addAction' => true,
            '__'        => fn($text) => $text,
        ]));
    }

    #[TestDox('module exists')]
    public function testArchiveModuleExists()
    {
        $this->assertInstanceOf(Archive::class, new Archive());
    }

    #[TestDox('module has correct slug')]
    public function testArchiveModuleHasCorrectSlug()
    {
        $this->assertEquals('archive', (new Archive())->slug);
    }

    #[TestDox('module is block compatible by default')]
    public function testArchiveModuleIsBlockCompatibleByDefault()
    {
        $this->assertTrue((new Archive())->isBlockCompatible);
    }

    #[TestDox('module data method returns an array')]
    public function testArchiveModuleDataMethodReturnsArray()
    {
        $module = new Archive();
        $this->assertIsArray($module->data());
    }

    #[TestDox('module has singular, plural and description labels')]
    public function testArchiveModuleHasLabels()
    {
        $module = new Archive();
        $this->assertNotEmpty($module->nameSingular);
        $this->assertNotEmpty($module->namePlural);
        $this->assertNotEmpty($module->description);
    }

    #[TestDox('module view template exists')]
    public function testArchiveModuleHasViewTemplate()
    {
        $module = new Archive();
        $this->assertFileExists(__DIR__ . '/views/' . $module->template());
    }

    #[TestDox('module has acf field group')]
    public function testArchiveModuleHasAcfFieldGroup()
    {
        $module   = new Archive();
        $fileName = 'mod-' . $module->slug;
        $acfDir   = __DIR__ . '/../../AcfFields';

        $this->assertFileExists($acfDir . '/json/' . $fileName . '.json');
        $this->assertFileExists($acfDir . '/php/' . ucfirst($fileName) . '.php');
    }
}
