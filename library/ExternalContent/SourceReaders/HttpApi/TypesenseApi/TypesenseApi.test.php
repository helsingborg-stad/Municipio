<?php

namespace Municipio\ExternalContent\SourceReaders\HttpApi\TypesenseApi;

use Municipio\ExternalContent\Config\SourceConfigInterface;
use Municipio\TestUtils\WpMockFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class TypesenseApiTest extends TestCase
{
    /**
     * @testdox class can be instantiated
     */
    public function testCanBeInstantiated()
    {
        $typesenseApi = new TypesenseApi($this->getConfigMock(), new FakeWpService());
        $this->assertInstanceOf(TypesenseApi::class, $typesenseApi);
    }

    /**
     * @testdox get() passes expected headers to the api request
     */
    public function testHeadersAreAddedToGetRequest()
    {
        $config = $this->getConfigMock();
        $config->method('getSourceTypesenseApiKey')->willReturn('test-api-key');
        $wpService    = new FakeWpService(['wpRemoteGet' => []]);
        $typesenseApi = new TypesenseApi($config, $wpService);

        $typesenseApi->get('test-endpoint');

        $this->assertEquals('test-api-key', $wpService->methodCalls['wpRemoteGet'][0][1]['headers']['X-TYPESENSE-API-KEY']);
        $this->assertEquals('application/json', $wpService->methodCalls['wpRemoteGet'][0][1]['headers']['Content-Type']);
    }

    /**
     * @testdox get() throw exception if remote call fails
     */
    public function testGetThrowsExceptionIfWpErrorIsReturned()
    {

        $wpService    = new FakeWpService([
            'wpRemoteGet' => WpMockFactory::createWpError(['get_error_message' => fn() => 'test-error']),
            'escHtml'     => fn($input) => $input,
        ]);
        $typesenseApi = new TypesenseApi($this->getConfigMock(), $wpService);

        $this->expectException(\RuntimeException::class);
        $typesenseApi->get('test-endpoint');
    }

    /**
     * @testdox get() returns ApiResponse object
     */
    public function testGetReturnsApiResponseObject()
    {
        $wpService = new FakeWpService([
            'wpRemoteGet' => ['body' => json_encode(['prop' => 'value']), 'headers' => ['test-header'], 'response' => ['code' => 200]],
        ]);

        $typesenseApi = new TypesenseApi($this->getConfigMock(), $wpService);

        $apiResponse = $typesenseApi->get('test-endpoint');

        $this->assertEquals(200, $apiResponse->getStatusCode());
        $this->assertEquals(['prop' => 'value'], $apiResponse->getBody());
        $this->assertEquals(['test-header'], $apiResponse->getHeaders());
    }

    /**
     * @testdox get() constructs correct Typesense URL
     */
    public function testGetConstructsCorrectUrl()
    {
        $config = $this->getConfigMock();

        $config->method('getSourceTypesenseProtocol')->willReturn('https');
        $config->method('getSourceTypesenseHost')->willReturn('test.com');
        $config->method('getSourceTypesensePort')->willReturn('1234');
        $config->method('getSourceTypesenseCollection')->willReturn('test-collection');

        $wpService    = new FakeWpService(['wpRemoteGet' => []]);
        $typesenseApi = new TypesenseApi($config, $wpService);

        $typesenseApi->get('test-endpoint');

        $this->assertEquals('https://test.com:1234/collections/test-collection/documents/search/test-endpoint', $wpService->methodCalls['wpRemoteGet'][0][0]);
    }

    private function getConfigMock(): SourceConfigInterface|MockObject
    {
        return $this->createMock(SourceConfigInterface::class);
    }
}
