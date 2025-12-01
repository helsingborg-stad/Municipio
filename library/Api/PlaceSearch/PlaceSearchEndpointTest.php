<?php

namespace Municipio\Api\PlaceSearch;

use Municipio\Api\PlaceSearch\Providers\Openstreetmap;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class PlaceSearchEndpointTest extends TestCase
{
    public function testCanBeInstantiated()
    {
        $placeSearchEndpoint = new PlaceSearchEndpoint(
            $this->getFakeWpService()
        );
        $this->assertInstanceOf(PlaceSearchEndpoint::class, $placeSearchEndpoint);
    }

    public function testHandleRegisterRestRouteReturnsTrue()
    {
        $placeSearchEndpoint = new PlaceSearchEndpoint($this->getFakeWpService());
        $result              = $placeSearchEndpoint->handleRegisterRestRoute();

        $this->assertTrue($result);
    }

    public function testResolveProviderReturnsOpenStreetMapProvider()
    {
        $placeSearchEndpoint = new PlaceSearchEndpoint($this->getFakeWpService());

        $provider = $placeSearchEndpoint->resolveProvider('openstreetmap');

        $this->assertInstanceOf(Openstreetmap::class, $provider);
    }

    public function testHandleRequestReturnsRequest()
    {
        $placeSearchEndpoint = new PlaceSearchEndpoint($this->getFakeWpService());

        $request = $this->createMock(\WP_REST_Request::class);
        $request->method('get_param')->willReturnMap([
            ['provider', 'openstreetmap'],
            ['q', ''],
        ]);

        $request->method('get_query_params')->willReturn([
            'q' => ''
        ]);

        $response = $placeSearchEndpoint->handleRequest($request);
        $this->assertInstanceOf(\WP_REST_Response::class, $response);
        $this->assertNull($response->data);
    }

    public function testCheckIsRequestInvalidReturnsErrorForMissingQ()
    {
        $placeSearchEndpoint = new PlaceSearchEndpoint($this->getFakeWpService());

        $request = $this->createMock(\WP_REST_Request::class);
        $request->method('get_query_params')->willReturn([
            'q' => ''
        ]);

        $result = $placeSearchEndpoint->checkIsRequestInvalid($request);

        $this->assertNotFalse($result);
        $this->assertIsString($result);
    }

    private function getFakeWpService($registerRestRoute = true)
    {
        return new FakeWpService([
            'registerRestRoute'    => $registerRestRoute,
            '__'                   => '',
            'wpRemoteGet'          => [],
            'isWpError'            => false,
            'wpRemoteRetrieveBody' => '',
            'getLocale'            => 'sv_SE',
            'applyFilters'         => function($tag, $value) { return $value; },
        ]);
    }
}
