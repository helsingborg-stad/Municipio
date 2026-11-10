<?php

declare(strict_types=1);

namespace Municipio\EasyRead;

use AcfService\AcfService;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class EasyReadFeatureTest extends TestCase
{
    #[TestDox('registers Easy Read field groups with Municipio ACF import and export')]
    public function testRegistersAcfExportGroups(): void
    {
        $feature = new EasyReadFeature(
            new FakeWpService(),
            $this->createMock(AcfService::class),
        );

        static::assertSame([
            'easy-reading' => 'group_58eb4fce51bb7',
            'easy-reading-options' => 'group_58eb9450b0a9f',
        ], $feature->registerAcfExports([]));
    }

    #[TestDox('ships importable PHP and JSON definitions for both Easy Read field groups')]
    public function testAcfFieldDefinitionsExist(): void
    {
        $fieldDirectory = dirname(__DIR__) . '/AcfFields';

        foreach (['easy-reading', 'easy-reading-options'] as $fieldGroup) {
            static::assertFileExists($fieldDirectory . '/php/' . $fieldGroup . '.php');
            static::assertFileExists($fieldDirectory . '/json/' . $fieldGroup . '.json');
        }
    }
}
