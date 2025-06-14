<?php

namespace Modularity\Module\FilesList;

use AcfService\Implementations\FakeAcfService;
use Modularity\Helper\AcfService;
use Modularity\Helper\WpService;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class FilesListTest extends TestCase
{
    #[TestDox('prepareFileData() does not throw warnings when settings is not an array')]
    public function testPrepareFileDataSettingsIsNotArray()
    {
        WpService::set(new FakeWpService(['sanitizeTitle' => fn($title) => $title]));
        AcfService::set(new FakeAcfService(['getField' => fn($field, $id) =>
            match ($field) {
                'file_list' => $this->getFileList(),
                'settings' => null
            }
        ]));

        $module     = $this->getMockBuilder(FilesList::class)->disableOriginalConstructor()->onlyMethods([])->getMock();
        $module->ID = 1;

        $this->assertCount(1, $module->prepareFileData());
    }

    private function getFileList(): array
    {
        return [
            [
                'file' => [
                    'title'       => 'title',
                    'url'         => 'url',
                    'description' => 'description',
                    'subtype'     => 'subtype',
                    'filesize'    => 123,
                ]
            ]
        ];
    }
}
