<?php

namespace Municipio\SchemaData\ExternalContent\SourceReaders;

use Municipio\SchemaData\ExternalContent\JsonToSchemaObjects\SimpleJsonConverter;
use Municipio\SchemaData\ExternalContent\SourceReaders\HttpApi\ApiGET;
use Municipio\SchemaData\ExternalContent\SourceReaders\HttpApi\ApiResponse;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class TypesenseSourceReaderTest extends TestCase
{
    /**
     * @testdox class can be instantiated
     */
    public function testCanBeInstantiated()
    {
        $typesenseSourceReader = new TypesenseSourceReader($this->getApiMock(), 'end/point', new SimpleJsonConverter());
        $this->assertInstanceOf(TypesenseSourceReader::class, $typesenseSourceReader);
    }

    /**
     * @testdox getSourceData calls api for data untill it gets a empty response
     */
    public function testGetSourceDataCallsApiForDataUntillItGetsANullResponse()
    {
        $api                   = $this->getApiMock([ $this->getApiResponseMock(['foo']), $this->getApiResponseMock(['foo']), $this->getApiResponseMock([], 404) ]);
        $typesenseSourceReader = new TypesenseSourceReader($api, 'end/point', new SimpleJsonConverter());

        $typesenseSourceReader->getSourceData();

        $this->assertStringContainsString('?page=1', $api->calls[0]);
        $this->assertStringContainsString('?page=2', $api->calls[1]);
    }

    /**
     * @testdox page number is appended correctly to endpoint with already defined GET parameters
     */
    public function testPageNumberIsAppendedCorrectlyToEndpointWithAlreadyDefinedGetParameters()
    {
        $api                   = $this->getApiMock([$this->getApiResponseMock(['foo']), $this->getApiResponseMock(['foo']), $this->getApiResponseMock([], 404)]);
        $typesenseSourceReader = new TypesenseSourceReader($api, 'end/point?param=value', new SimpleJsonConverter());

        $typesenseSourceReader->getSourceData();

        $this->assertStringContainsString('&page=1', $api->calls[0]);
        $this->assertStringContainsString('&page=2', $api->calls[1]);
    }

    /**
     * @testdox per_page param is appended to endpoint
     */
    public function testPerPageParamIsAppendedToEndpoint()
    {
        $api                   = $this->getApiMock([$this->getApiResponseMock(['foo']), $this->getApiResponseMock()]);
        $typesenseSourceReader = new TypesenseSourceReader($api, 'end/point', new SimpleJsonConverter());

        $typesenseSourceReader->getSourceData();

        $this->assertStringContainsString('&per_page=250', $api->calls[0]);
    }

    /**
     * @testdox query param is appended to endpoint
     */
    public function testQueryParamIsAppendedToEndpoint()
    {
        $api                   = $this->getApiMock([$this->getApiResponseMock(['foo']), $this->getApiResponseMock()]);
        $typesenseSourceReader = new TypesenseSourceReader($api, 'end/point', new SimpleJsonConverter());

        $typesenseSourceReader->getSourceData();

        $this->assertStringContainsString('&q=*', $api->calls[0]);
    }

    private function getApiMock(array $consecutiveReturns = []): ApiGET
    {
        return new class ($consecutiveReturns) implements ApiGET
        {
            public array $calls = [];
            private $nbrOfCalls = 0;

            public function __construct(private array $consecutiveReturns)
            {
            }

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
