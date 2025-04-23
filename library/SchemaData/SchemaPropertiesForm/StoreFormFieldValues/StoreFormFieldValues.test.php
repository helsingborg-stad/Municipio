<?php

namespace Municipio\SchemaData\SchemaPropertiesForm\StoreFormFieldValues;

use AcfService\Implementations\FakeAcfService;
use Municipio\Config\Features\SchemaData\Contracts\TryGetSchemaTypeFromPostType;
use Municipio\SchemaData\Utils\GetEnabledSchemaTypesInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class StoreFormFieldValuesTest extends TestCase
{
    /**
     * @testdox class can be instantiated
     */
    public function testClassCanBeInstantiated()
    {
        $this->assertInstanceOf(StoreFormFieldValues::class, new StoreFormFieldValues(
            new FakeWpService(),
            new FakeAcfService(),
            $this->getSchemaTypeService(),
            $this->getEnabledSchemaTypesService()
        ));
    }

    /**
     * @testdox runs on hook for saving post
     */
    public function testRunsOnHookForSavingPost()
    {
        $wpService                 = new FakeWpService(['addAction' => true]);
        $schemaTypeService         = $this->getSchemaTypeService();
        $enabledSchemaTypesService = $this->getEnabledSchemaTypesService();

        $storeFormFieldValues = new StoreFormFieldValues($wpService, new FakeAcfService(), $schemaTypeService, $enabledSchemaTypesService);
        $storeFormFieldValues->addHooks();

        $this->assertEquals('acf/save_post', $wpService->methodCalls['addAction'][0][0]);
    }

    /**
     * @testdox saves schema data if schemaType and allowed properties are found
     */
    public function testSavesSchemaDataIfSchemaTypeAndAllowedPropertiesAreFound()
    {
        $postId            = 123;
        $postType          = 'post';
        $schemaType        = 'schemaType';
        $allowedProperties = ['name', 'description'];
        $schemaObject      = ['name' => 'Old Name', 'description' => 'Old Description'];

        $_POST['_wpnonce'] = 'valid_nonce';
        $_POST['acf']      = [
            'schema_name'        => 'New Name',
            'schema_description' => 'New Description',
        ];

        $wpService = new FakeWpService([
            'getPostType'    => $postType,
            'getPostMeta'    => $schemaObject,
            'updatePostMeta' => true,
            'wpVerifyNonce'  => true,
        ]);

        $schemaTypeService         = $this->getSchemaTypeService();
        $enabledSchemaTypesService = $this->getEnabledSchemaTypesService();

        $schemaTypeService->method('tryGetSchemaTypeFromPostType')->willReturn($schemaType);
        $enabledSchemaTypesService->method('getEnabledSchemaTypesAndProperties')->willReturn([$schemaType => $allowedProperties]);

        $storeFormFieldValues = new StoreFormFieldValues($wpService, new FakeAcfService(), $schemaTypeService, $enabledSchemaTypesService);

        // Simulate the action
        $storeFormFieldValues->saveSchemaData($postId);

        // Check if the schema object was updated correctly
        $expectedSchemaObject = [
            'name'        => 'New Name',
            'description' => 'New Description',
        ];

        $this->assertEquals($expectedSchemaObject, $wpService->methodCalls['updatePostMeta'][0][2]);
    }

    /**
     * @testdox creates a new schema object if none is found in post meta
     */
    public function testCreatesANewSchemaObjectIfNoneIsFoundInPostMeta()
    {
        $postId            = 123;
        $postType          = 'post';
        $schemaType        = 'Thing';
        $allowedProperties = ['name', 'description'];
        $schemaObjectInDb  = '';

        $_POST['_wpnonce'] = 'valid_nonce';
        $_POST['acf']      = [
            'schema_name'        => 'New Name',
            'schema_description' => 'New Description',

        ];

        $wpService = new FakeWpService([
            'getPostType'    => $postType,
            'getPostMeta'    => $schemaObjectInDb,
            'updatePostMeta' => true,
            'wpVerifyNonce'  => true,
        ]);

        $schemaTypeService         = $this->getSchemaTypeService();
        $enabledSchemaTypesService = $this->getEnabledSchemaTypesService();

        $schemaTypeService->method('tryGetSchemaTypeFromPostType')->willReturn($schemaType);
        $enabledSchemaTypesService->method('getEnabledSchemaTypesAndProperties')->willReturn([$schemaType => $allowedProperties]);

        $storeFormFieldValues = new StoreFormFieldValues($wpService, new FakeAcfService(), $schemaTypeService, $enabledSchemaTypesService);

        // Simulate the action
        $storeFormFieldValues->saveSchemaData($postId);

        $this->assertEquals('Thing', $wpService->methodCalls['updatePostMeta'][0][2]['@type']);
        $this->assertEquals('New Name', $wpService->methodCalls['updatePostMeta'][0][2]['name']);
        $this->assertEquals('New Description', $wpService->methodCalls['updatePostMeta'][0][2]['description']);
    }

    /**
     * @testdox does not save schema data if schemaType is not found
     */
    public function testDoesNotSaveSchemaDataIfSchemaTypeNotFound()
    {
        $postId            = 123;
        $postType          = 'post';
        $schemaType        = null;
        $allowedProperties = ['name', 'description'];
        $schemaObject      = ['name' => 'Old Name', 'description' => 'Old Description'];
        $_POST['_wpnonce'] = 'valid_nonce';

        $wpService = new FakeWpService([
            'getPostType'    => $postType,
            'getPostMeta'    => $schemaObject,
            'updatePostMeta' => true,
            'wpVerifyNonce'  => true,
        ]);

        $schemaTypeService         = $this->getSchemaTypeService();
        $enabledSchemaTypesService = $this->getEnabledSchemaTypesService();

        $schemaTypeService->method('tryGetSchemaTypeFromPostType')->willReturn($schemaType);
        $enabledSchemaTypesService->method('getEnabledSchemaTypesAndProperties')->willReturn([$schemaType => $allowedProperties]);

        $storeFormFieldValues = new StoreFormFieldValues($wpService, new FakeAcfService(), $schemaTypeService, $enabledSchemaTypesService);

        // Simulate the action
        $storeFormFieldValues->saveSchemaData($postId);

        $this->assertArrayNotHasKey('updatePostMeta', $wpService->methodCalls);
    }

    /**
     * @testdox does not save schema data if allowed properties are not set
     */
    public function testDoesNotSaveSchemaDataIfAllowedPropertiesNotSet()
    {
        $postId            = 123;
        $postType          = 'post';
        $schemaType        = 'schemaType';
        $allowedProperties = [];
        $schemaObject      = ['name' => 'Old Name', 'description' => 'Old Description'];
        $_POST['_wpnonce'] = 'valid_nonce';

        $wpService = new FakeWpService([
            'getPostType'    => $postType,
            'getPostMeta'    => $schemaObject,
            'updatePostMeta' => true,
            'wpVerifyNonce'  => true,
        ]);

        $schemaTypeService         = $this->getSchemaTypeService();
        $enabledSchemaTypesService = $this->getEnabledSchemaTypesService();

        $schemaTypeService->method('tryGetSchemaTypeFromPostType')->willReturn($schemaType);
        $enabledSchemaTypesService->method('getEnabledSchemaTypesAndProperties')->willReturn([$schemaType => $allowedProperties]);

        $storeFormFieldValues = new StoreFormFieldValues($wpService, new FakeAcfService(), $schemaTypeService, $enabledSchemaTypesService);

        // Simulate the action
        $storeFormFieldValues->saveSchemaData($postId);

        $this->assertArrayNotHasKey('updatePostMeta', $wpService->methodCalls);
    }

    private function getSchemaTypeService(): TryGetSchemaTypeFromPostType|MockObject
    {
        return $this->createMock(TryGetSchemaTypeFromPostType::class);
    }

    private function getEnabledSchemaTypesService(): GetEnabledSchemaTypesInterface|MockObject
    {
        return $this->createMock(GetEnabledSchemaTypesInterface::class);
    }
}
