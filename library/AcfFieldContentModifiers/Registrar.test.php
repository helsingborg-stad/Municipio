<?php

namespace Municipio\AcfFieldContentModifiers;

use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class RegistrarTest extends TestCase
{
    public function testFilterIsAdded()
    {
        $wpService = new FakeWpService();
        $registrar = new Registrar($wpService);

        $registrar->registerModifier($this->getTestModifier());

        $this->assertEquals('acf/load_field/key=test', $wpService->methodCalls['addAction'][0][0]);
    }

    private function getTestModifier(): AcfFieldContentModifierInterface
    {
        return new class implements AcfFieldContentModifierInterface {
            public function modifyFieldContent(array $field): array
            {
                return [];
            }

            public function getFieldKey(): string
            {
                return 'test';
            }
        };
    }
}
