<?php

namespace Municipio\SchemaData\ExternalContent\SourceReaders;

use Municipio\Schema\Schema;
use Municipio\SchemaData\ExternalContent\JsonToSchemaObjects\JsonToSchemaObjects;
use Municipio\SchemaData\ExternalContent\SourceReaders\HttpApi\ApiGET;
use Municipio\SchemaData\ExternalContent\SourceReaders\HttpApi\ApiResponse;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class TypesenseSourceReaderTest extends TestCase
{
    #[TestDox('class can be instantiated')]
    public function testCanBeInstantiated()
    {
        $typesenseSourceReader = new TypesenseSourceReader($this->getApiMock(), 'end/point', new JsonToSchemaObjects());
        $this->assertInstanceOf(TypesenseSourceReader::class, $typesenseSourceReader);
    }

    #[TestDox('getSourceData calls api for data untill it gets a empty response')]
    public function testGetSourceDataCallsApiForDataUntillItGetsANullResponse()
    {
        $schemaObjectJson = Schema::thing()->jsonSerialize();
        $api = $this->getApiMock([$this->getApiResponseMock([$schemaObjectJson]), $this->getApiResponseMock([$schemaObjectJson]), $this->getApiResponseMock([], 200)]);
        $typesenseSourceReader = new TypesenseSourceReader($api, 'end/point', new JsonToSchemaObjects());

        $typesenseSourceReader->getSourceData();

        $this->assertStringContainsString('?page=1', $api->calls[0]);
        $this->assertStringContainsString('?page=2', $api->calls[1]);
    }

    #[TestDox('page number is appended correctly to endpoint with already defined GET parameters')]
    public function testPageNumberIsAppendedCorrectlyToEndpointWithAlreadyDefinedGetParameters()
    {
        $schemaObjectJson = Schema::thing()->jsonSerialize();
        $api = $this->getApiMock([$this->getApiResponseMock([$schemaObjectJson]), $this->getApiResponseMock([$schemaObjectJson]), $this->getApiResponseMock([], 200)]);
        $typesenseSourceReader = new TypesenseSourceReader($api, 'end/point?param=value', new JsonToSchemaObjects());

        $typesenseSourceReader->getSourceData();

        $this->assertStringContainsString('&page=1', $api->calls[0]);
        $this->assertStringContainsString('&page=2', $api->calls[1]);
    }

    #[TestDox('per_page param is appended to endpoint')]
    public function testPerPageParamIsAppendedToEndpoint()
    {
        $schemaObjectJson = Schema::thing()->jsonSerialize();
        $api = $this->getApiMock([$this->getApiResponseMock([$schemaObjectJson]), $this->getApiResponseMock()]);
        $typesenseSourceReader = new TypesenseSourceReader($api, 'end/point', new JsonToSchemaObjects());

        $typesenseSourceReader->getSourceData();

        $this->assertStringContainsString('&per_page=250', $api->calls[0]);
    }

    #[TestDox('query param is appended to endpoint')]
    public function testQueryParamIsAppendedToEndpoint()
    {
        $schemaObjectJson = Schema::thing()->jsonSerialize();
        $api = $this->getApiMock([$this->getApiResponseMock([$schemaObjectJson]), $this->getApiResponseMock()]);
        $typesenseSourceReader = new TypesenseSourceReader($api, 'end/point', new JsonToSchemaObjects());

        $typesenseSourceReader->getSourceData();

        $this->assertStringContainsString('&q=*', $api->calls[0]);
    }

    #[TestDox('id is stripped from schema object')]
    public function testIdIsStrippedFromSchemaObject()
    {
        $bodyWithObjects = [
            [
                'document' => Schema::thing()->setProperty('id', '123')->jsonSerialize(),
            ],
        ];
        $api = $this->getApiMock([$this->getApiResponseMock($bodyWithObjects), $this->getApiResponseMock()]);
        $typesenseSourceReader = new TypesenseSourceReader($api, 'end/point', new JsonToSchemaObjects());

        $schemaObjects = $typesenseSourceReader->getSourceData();

        $this->assertCount(1, $schemaObjects);
        $this->assertNull($schemaObjects[0]->getProperty('id'));
    }

    private function getApiMock(array $consecutiveReturns = []): ApiGET
    {
        return new class($consecutiveReturns) implements ApiGET {
            public array $calls = [];
            private $nbrOfCalls = 0;

            public function __construct(
                private array $consecutiveReturns,
            ) {}

            public function get(string $endpoint): ApiResponse
            {
                $this->calls[] = $endpoint;
                return $this->consecutiveReturns[$this->nbrOfCalls++];
            }
        };
    }

    private function getApiResponseMock(array $body = [], int $statusCode = 200, array $headers = []): ApiResponse|MockObject
    {
        $apiResponse = $this->createMock(ApiResponse::class);
        $apiResponse->method('getBody')->willReturn($body);
        $apiResponse->method('getStatusCode')->willReturn($statusCode);
        $apiResponse->method('getHeaders')->willReturn($headers);

        return $apiResponse;
    }
}
