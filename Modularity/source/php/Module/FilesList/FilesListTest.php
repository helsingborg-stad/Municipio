<?php

declare(strict_types=1);

namespace Modularity\Module\FilesList;

use AcfService\Implementations\FakeAcfService;
use Modularity\Helper\AcfService;
use Modularity\Helper\WpService;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class FilesListTest extends TestCase
{
    #[TestDox('prepareFileData does not throw if file size is not numeric')]
    public function testPrepareFileDataWithNonNumericSize()
    {
        WpService::set(new FakeWpService(['addAction' => true, 'sanitizeTitle' => fn($string) => $string]));
        AcfService::set(new FakeAcfService(['getFields' => ['file_list' => [
            [
                'file' => [
                    'url' => 'https://example.com/file.pdf',
                    'title' => 'file.pdf',
                    'description' => 'Example file',
                    'subtype' => 'pdf',
                    'filesize' => null,
                ],
            ],
        ]]]));

        $module = new FilesList();
        $rows = $module->prepareFileData();

        $this->assertEquals('0 B', $rows[0]['meta'][1]);
    }
}
