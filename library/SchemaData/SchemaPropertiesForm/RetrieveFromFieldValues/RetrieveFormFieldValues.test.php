<?php

namespace Municipio\SchemaData\SchemaPropertiesForm\RetrieveFromFieldValues;

use Municipio\Actions\Admin\PostPageEditAction;
use Municipio\Config\Features\SchemaData\Contracts\TryGetSchemaTypeFromPostType;
use Municipio\SchemaData\Utils\GetEnabledSchemaTypesInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class RetrieveFormFieldValuesTest extends TestCase
{
    /**
     * @testdox class can be instantiated
     */
    public function testClassCanBeInstantiated()
    {
        $this->assertInstanceOf(RetrieveFormFieldValues::class, new RetrieveFormFieldValues(
            new FakeWpService(),
            $this->getSchemaTypeService(),
            $this->getEnabledSchemaTypesService()
        ));
    }

    /**
     * @testdox runs on hook from PostPageEditAction
     */
    public function testRunsOnHookFromPostPageEditAction()
    {
        $wpService                 = new FakeWpService(['addAction' => true]);
        $schemaTypeService         = $this->getSchemaTypeService();
        $enabledSchemaTypesService = $this->getEnabledSchemaTypesService();

        $retrieveFormFieldValues = new RetrieveFormFieldValues($wpService, $schemaTypeService, $enabledSchemaTypesService);
        $retrieveFormFieldValues->addHooks();

        $this->assertEquals(PostPageEditAction::ACTION, $wpService->methodCalls['addAction'][0][0]);
    }

    /**
     * @testdox retrieves field values from schemaObject if schemaType is found and allowed properties are set
     */
    public function testRetrievesFieldValuesFromSchemaObject()
    {
        $postId            = 123;
        $postType          = 'post';
        $schemaType        = 'schemaType';
        $allowedProperties = ['name', 'description'];
        $schemaObject      = ['name' => 'Test Name', 'description' => 'Test Description'];

        $wpService = new FakeWpService([
            'getPostMeta' => $schemaObject,
            'addAction'   => true,
            'addFilter'   => true,
        ]);

        $schemaTypeService         = $this->getSchemaTypeService();
        $enabledSchemaTypesService = $this->getEnabledSchemaTypesService();

        $schemaTypeService->method('tryGetSchemaTypeFromPostType')->willReturn($schemaType);
        $enabledSchemaTypesService->method('getEnabledSchemaTypesAndProperties')->willReturn([$schemaType => $allowedProperties]);

        $retrieveFormFieldValues = new RetrieveFormFieldValues($wpService, $schemaTypeService, $enabledSchemaTypesService);

        // Simulate the action
        $retrieveFormFieldValues->retrieveFieldValues($postId, $postType);

        // Check if the correct properties were retrieved
        $filteredValue1 = $wpService->methodCalls['addFilter'][0][1]('defaultValue');

        $this->assertEquals($schemaObject['name'], $filteredValue1);
        $this->assertEquals($schemaObject['description'], $wpService->methodCalls['addFilter'][1][1]('defaultValue'));
    }

    /**
     * @testdox does not retrieve field values if schemaType is not found
     */
    public function testDoesNotRetrieveFieldValuesIfSchemaTypeNotFound()
    {
        $postId            = 123;
        $postType          = 'post';
        $schemaType        = null;
        $allowedProperties = ['name', 'description'];
        $schemaObject      = ['name' => 'Test Name', 'description' => 'Test Description'];

        $wpService = new FakeWpService([
            'getPostMeta' => $schemaObject,
            'addAction'   => true,
            'addFilter'   => true,
        ]);

        $schemaTypeService         = $this->getSchemaTypeService();
        $enabledSchemaTypesService = $this->getEnabledSchemaTypesService();

        $schemaTypeService->method('tryGetSchemaTypeFromPostType')->willReturn($schemaType);
        $enabledSchemaTypesService->method('getEnabledSchemaTypesAndProperties')->willReturn([$schemaType => $allowedProperties]);

        $retrieveFormFieldValues = new RetrieveFormFieldValues($wpService, $schemaTypeService, $enabledSchemaTypesService);

        // Simulate the action
        $retrieveFormFieldValues->retrieveFieldValues($postId, $postType);

        $this->assertArrayNotHasKey('addFilter', $wpService->methodCalls);
    }

    /**
     * @testdox does not retrieve field values if allowed properties are not set
     */
    public function testDoesNotRetrieveFieldValuesIfAllowedPropertiesNotSet()
    {
        $postId            = 123;
        $postType          = 'post';
        $schemaType        = 'schemaType';
        $allowedProperties = [];
        $schemaObject      = ['name' => 'Test Name', 'description' => 'Test Description'];

        $wpService = new FakeWpService([
            'getPostMeta' => $schemaObject,
            'addAction'   => true,
            'addFilter'   => true,
        ]);

        $schemaTypeService         = $this->getSchemaTypeService();
        $enabledSchemaTypesService = $this->getEnabledSchemaTypesService();

        $schemaTypeService->method('tryGetSchemaTypeFromPostType')->willReturn($schemaType);
        $enabledSchemaTypesService->method('getEnabledSchemaTypesAndProperties')->willReturn([$schemaType => $allowedProperties]);

        $retrieveFormFieldValues = new RetrieveFormFieldValues($wpService, $schemaTypeService, $enabledSchemaTypesService);

        // Simulate the action
        $retrieveFormFieldValues->retrieveFieldValues($postId, $postType);

        $this->assertArrayNotHasKey('addFilter', $wpService->methodCalls);
    }

    /**
     * @testdox does not retrieve field values if schemaObject is not found
     */
    public function testDoesNotRetrieveFieldValuesIfSchemaObjectNotFound()
    {
        $postId            = 123;
        $postType          = 'post';
        $schemaType        = 'schemaType';
        $allowedProperties = ['name', 'description'];
        $schemaObject      = null;

        $wpService = new FakeWpService([
            'getPostMeta' => $schemaObject,
            'addAction'   => true,
            'addFilter'   => true,
        ]);

        $schemaTypeService         = $this->getSchemaTypeService();
        $enabledSchemaTypesService = $this->getEnabledSchemaTypesService();

        $schemaTypeService->method('tryGetSchemaTypeFromPostType')->willReturn($schemaType);
        $enabledSchemaTypesService->method('getEnabledSchemaTypesAndProperties')->willReturn([$schemaType => $allowedProperties]);

        $retrieveFormFieldValues = new RetrieveFormFieldValues($wpService, $schemaTypeService, $enabledSchemaTypesService);

        // Simulate the action
        $retrieveFormFieldValues->retrieveFieldValues($postId, $postType);

        $this->assertArrayNotHasKey('addFilter', $wpService->methodCalls);
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
