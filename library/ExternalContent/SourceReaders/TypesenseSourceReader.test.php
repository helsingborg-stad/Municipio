<?php

namespace Municipio\ExternalContent\SourceReaders;

use Municipio\ExternalContent\SourceReaders\HttpApi\ApiGET;
use Municipio\ExternalContent\SourceReaders\HttpApi\ApiResponse;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class TypesenseSourceReaderTest extends TestCase
{
    /**
     * @testdox class can be instantiated
     */
    public function testCanBeInstantiated()
    {
        $typesenseSourceReader = new TypesenseSourceReader($this->getApiMock(), 'end/point');
        $this->assertInstanceOf(TypesenseSourceReader::class, $typesenseSourceReader);
    }

    /**
     * @testdox getSourceData calls api for data untill it gets a null response
     */
    public function testGetSourceDataCallsApiForDataUntillItGetsANullResponse()
    {
        $api                   = $this->getApiMock([ $this->getApiResponseMock(), $this->getApiResponseMock([], 404) ]);
        $typesenseSourceReader = new TypesenseSourceReader($api, 'end/point');

        $typesenseSourceReader->getSourceData();

        $this->assertEquals('end/point?page=1', $api->calls[0]);
        $this->assertEquals('end/point?page=2', $api->calls[1]);
    }

    /**
     * @testdox page number is appended correctly to endpoint with already defined GET parameters
     */
    public function testPageNumberIsAppendedCorrectlyToEndpointWithAlreadyDefinedGetParameters()
    {
        $api                   = $this->getApiMock([$this->getApiResponseMock(), $this->getApiResponseMock([], 404)]);
        $typesenseSourceReader = new TypesenseSourceReader($api, 'end/point?param=value');

        $typesenseSourceReader->getSourceData();

        $this->assertEquals('end/point?param=value&page=1', $api->calls[0]);
        $this->assertEquals('end/point?param=value&page=2', $api->calls[1]);
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
