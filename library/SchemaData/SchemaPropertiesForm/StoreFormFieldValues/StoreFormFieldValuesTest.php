<?php

namespace Municipio\SchemaData\SchemaPropertiesForm\StoreFormFieldValues;

use Municipio\Config\Features\SchemaData\Contracts\TryGetSchemaTypeFromPostType;
use Municipio\PostObject\Factory\PostObjectFromWpPostFactoryInterface;
use Municipio\PostObject\PostObjectInterface;
use Municipio\Schema\Schema;
use Municipio\SchemaData\SchemaPropertiesForm\StoreFormFieldValues\FieldMapper\FieldMapperInterface;
use Municipio\SchemaData\SchemaPropertiesForm\StoreFormFieldValues\NonceValidation\PostNonceValidatorInterface;
use Municipio\SchemaData\SchemaPropertiesForm\StoreFormFieldValues\SchemaPropertiesFromMappedFields\SchemaPropertiesFromMappedFieldsInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use WP_Post;
use WpService\Implementations\FakeWpService;

class StoreFormFieldValuesTest extends TestCase
{
    protected function setUp(): void
    {
        if (!defined('ARRAY_A')) {
            define('ARRAY_A', 'ARRAY_A');
        }
        parent::setUp();
    }

    /**
     * @testdox class can be instantiated
     */
    public function testClassCanBeInstantiated()
    {
        $this->assertInstanceOf(StoreFormFieldValues::class, new StoreFormFieldValues(
            new FakeWpService(),
            $this->getSchemaTypeService(),
            $this->getPostNonceValidator(),
            $this->getFieldMapper(),
            $this->getSchemaPropertiesFromMappedFields(),
            $this->getPostObjectFactory(),
        ));
    }

    /**
     * @testdox attaches to the acf/update_value/name=schemaData hook
     */
    public function testAddsHooks()
    {
        $wpService = new FakeWpService(['addFilter' => true]);
        $sut       = new StoreFormFieldValues(
            $wpService,
            $this->getSchemaTypeService(),
            $this->getPostNonceValidator(),
            $this->getFieldMapper(),
            $this->getSchemaPropertiesFromMappedFields(),
            $this->getPostObjectFactory(),
        );

        $sut->addHooks();

        $this->assertEquals('acf/update_value/name=schemaData', $wpService->methodCalls['addFilter'][0][0]);
    }

    /**
     * @testdox saveSchemaData returns original value if nonce is invalid
     */
    public function testSaveSchemaDataReturnsOriginalValueIfNonceInvalid()
    {
        $wpService      = new FakeWpService();
        $nonceValidator = $this->getPostNonceValidator();
        $nonceValidator->method('isValid')->willReturn(false);

        $sut = new StoreFormFieldValues(
            $wpService,
            $this->getSchemaTypeService(),
            $nonceValidator,
            $this->getFieldMapper(),
            $this->getSchemaPropertiesFromMappedFields(),
            $this->getPostObjectFactory(),
        );

        $originalValue     = ['foo' => 'bar'];
        $_POST['_wpnonce'] = 'bad-nonce';

        $result = $sut->saveSchemaData($originalValue, 123, ['key' => 'field_1'], null);

        $this->assertSame($originalValue, $result);
    }

    /**
     * @testdox saveSchemaData returns original value if schema type is not found
     */
    public function testSaveSchemaDataReturnsOriginalValueIfSchemaTypeNotFound()
    {
        $wpService      = new FakeWpService(['getPostType' => 'post']);
        $nonceValidator = $this->getPostNonceValidator();
        $nonceValidator->method('isValid')->willReturn(true);

        $schemaTypeService = $this->getSchemaTypeService();
        $schemaTypeService->method('tryGetSchemaTypeFromPostType')->willReturn(null);

        $sut = new StoreFormFieldValues(
            $wpService,
            $schemaTypeService,
            $nonceValidator,
            $this->getFieldMapper(),
            $this->getSchemaPropertiesFromMappedFields(),
            $this->getPostObjectFactory(),
        );

        $originalValue     = ['foo' => 'bar'];
        $_POST['_wpnonce'] = 'good-nonce';

        $result = $sut->saveSchemaData($originalValue, 123, ['key' => 'field_1'], null);

        $this->assertSame($originalValue, $result);
    }

    /**
     * @testdox saveSchemaData returns original value if posted data is empty
     */
    public function testSaveSchemaDataReturnsOriginalValueIfPostedDataEmpty()
    {
        $wpService      = new FakeWpService(['getPostType' => 'post']);
        $nonceValidator = $this->getPostNonceValidator();
        $nonceValidator->method('isValid')->willReturn(true);

        $sut = new StoreFormFieldValues(
            $wpService,
            $this->getSchemaTypeService(),
            $nonceValidator,
            $this->getFieldMapper(),
            $this->getSchemaPropertiesFromMappedFields(),
            $this->getPostObjectFactory(),
        );

        $originalValue     = ['foo' => 'bar'];
        $_POST['_wpnonce'] = 'good-nonce';
        $_POST['acf']      = []; // No data for the field key

        $result = $sut->saveSchemaData($originalValue, 123, ['key' => 'field_1'], null);

        $this->assertSame($originalValue, $result);
    }

    /**
     * @testdox saveSchemaData returns schema object if all conditions are met
     */
    public function testSaveSchemaDataReturnsSchemaObjectIfAllConditionsMet()
    {
        $wpService = new FakeWpService([
            'getPostType' => 'post',
            'getPost'     => new WP_Post([]),
        ]);

        $nonceValidator = $this->getPostNonceValidator();
        $nonceValidator->method('isValid')->willReturn(true);

        $schemaTypeService = $this->getSchemaTypeService();
        $schemaTypeService->method('tryGetSchemaTypeFromPostType')->willReturn('Store');

        $fieldMapper = $this->getFieldMapper();
        $fieldMapper->method('getMappedFields')->willReturn([]);

        $schemaPropertiesFromMappedFields = $this->getSchemaPropertiesFromMappedFields();
        $schemaPropertiesFromMappedFields->method('apply')->willReturn(Schema::store());

        $postObjectMock = $this->createMock(PostObjectInterface::class);
        $postObjectMock->method('getSchema')->willReturn(Schema::store());

        $postObjectFactory = $this->getPostObjectFactory();
        $postObjectFactory->method('create')->willReturn($postObjectMock);

        $sut = new StoreFormFieldValues(
            $wpService,
            $schemaTypeService,
            $nonceValidator,
            $fieldMapper,
            $schemaPropertiesFromMappedFields,
            $postObjectFactory,
        );

        $_POST['_wpnonce'] = 'good-nonce';
        $_POST['acf']      = [
            'field_1' => [
                'sub_fields' => [
                    'name'                      => 'Store Name',
                    'openingHoursSpecification' => [
                        [
                            '@type'     => 'OpeningHoursSpecification',
                            'dayOfWeek' => ['Monday', 'Tuesday'],
                            'opens'     => '09:00',
                            'closes'    => '18:00',
                        ],
                    ],
                ],
            ],
        ];

        $result = $sut->saveSchemaData([], 123, ['key' => 'field_1', 'sub_fields' => []], null);

        $this->assertIsArray($result);
        $this->assertEquals('Store', $result['@type']);
    }

    private function getSchemaTypeService(): TryGetSchemaTypeFromPostType|MockObject
    {
        $schemaTypeService = $this->createMock(TryGetSchemaTypeFromPostType::class);
        $schemaTypeService->method('tryGetSchemaTypeFromPostType')
            ->willReturn('Store');

        return $schemaTypeService;
    }

    private function getFieldMapper(): FieldMapperInterface|MockObject
    {
        return $this->createMock(FieldMapperInterface::class);
    }

    private function getSchemaPropertiesFromMappedFields(): SchemaPropertiesFromMappedFieldsInterface|MockObject
    {
        return $this->createMock(SchemaPropertiesFromMappedFieldsInterface::class);
    }

    private function getPostNonceValidator(): PostNonceValidatorInterface|MockObject
    {
        return $this->createMock(PostNonceValidatorInterface::class);
    }

    private function getPostObjectFactory(): PostObjectFromWpPostFactoryInterface|MockObject
    {
        return $this->createMock(PostObjectFromWpPostFactoryInterface::class);
    }
}
