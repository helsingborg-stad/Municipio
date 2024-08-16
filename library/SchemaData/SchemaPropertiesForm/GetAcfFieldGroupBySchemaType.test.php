<?php

namespace Municipio\SchemaData\SchemaPropertiesForm;

use Municipio\SchemaData\Utils\GetSchemaPropertiesWithParamTypesInterface;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class GetAcfFieldGroupBySchemaTypeTest extends TestCase
{
    public function testReturnsEmptyArrayIfSchemaTypeIsInvalid(): void
    {
        $sut = new GetAcfFieldGroupBySchemaType(
            new FakeWpService(),
            $this->getSchemaPropertiesWithParamTypes(),
            $this->getFormFieldsBySchemaPropertiesInterface()
        );

        $this->assertEmpty($sut->getAcfFieldGroup('InvalidSchemaType'));
    }

    public function testReturnsFieldGroupWithFieldsIfSchemaTypeIsValid(): void
    {
        $sut = new GetAcfFieldGroupBySchemaType(
            new FakeWpService(),
            $this->getSchemaPropertiesWithParamTypes(),
            $this->getFormFieldsBySchemaPropertiesInterface()
        );

        $this->assertNotEmpty($sut->getAcfFieldGroup('Organization')['fields']);
    }

    private function getSchemaPropertiesWithParamTypes(): GetSchemaPropertiesWithParamTypesInterface
    {
        return new class implements GetSchemaPropertiesWithParamTypesInterface {
            public function getSchemaPropertiesWithParamTypes(string $schemaType): array
            {
                return ['telephone' => ['Text', 'Text[]']];
            }
        };
    }

    private function getFormFieldsBySchemaPropertiesInterface(): GetFormFieldsBySchemaPropertiesInterface
    {
        return new class implements GetFormFieldsBySchemaPropertiesInterface {
            public function getFormFieldsBySchemaProperties(string $schemaType, array $schemaProperties): array
            {
                return [
                    [
                        'key'   => 'field_1',
                        'label' => 'Telephone',
                        'name'  => 'telephone',
                        'type'  => 'text',
                    ],
                ];
            }
        };
    }
}
