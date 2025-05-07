<?php

namespace Municipio\Api\PlaceSearch\Providers;

use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class OpenstreetmapTest extends TestCase
{
    public function testCanBeInstantiated()
    {
        $openstreetmap = new Openstreetmap(
            $this->getFakeWpService()
        );
        $this->assertInstanceOf(Openstreetmap::class, $openstreetmap);
    }

    public function testSearchReturnsAnEmptyArrayIfWpError()
    {
        $openstreetmap = new Openstreetmap(
            $this->getFakeWpService(new \WP_Error(), true)
        );

        $result = $openstreetmap->search(['q' => 'test']);

        $this->assertEquals([], $result);
    }

    public function testCreateEndpointUrlWithSearch()
    {
        $openstreetmap = new Openstreetmap($this->getFakeWpService());

        $url = $openstreetmap->createEndpointUrl(['q' => 'test']);

        $this->assertStringContainsString('search?', $url);
        $this->assertStringContainsString('format=json', $url);
        $this->assertStringContainsString('q=test', $url);
    }

    public function testCreateEndpointUrlWithReverse()
    {
        $openstreetmap = new Openstreetmap($this->getFakeWpService());

        $url = $openstreetmap->createEndpointUrl([
            'reverse' => true,
            'lat'     => '59.3293',
            'lon'     => '18.0686'
        ]);

        $this->assertStringContainsString('reverse?', $url);
        $this->assertStringContainsString('format=json', $url);
        $this->assertStringContainsString('lat=59.3293', $url);
        $this->assertStringContainsString('lon=18.0686', $url);
    }

    public function testSearchReturnsTransformedResults()
    {
        $mockResponse = [
            [
                'lat'          => '59.3293',
                'lon'          => '18.0686',
                'display_name' => 'Stockholm, Sweden'
            ]
        ];

        $openstreetmap = new Openstreetmap(
            $this->getFakeWpService($mockResponse, json_encode($mockResponse))
        );

        $result = $openstreetmap->search(['q' => 'Stockholm']);

        $place = $result[0];
        $this->assertEquals('59.3293', $place['latitude']);
        $this->assertEquals('18.0686', $place['longitude']);
        $this->assertEquals('Stockholm, Sweden', $place['address']);
    }

    private function getFakeWpService($wpRemoteGet = [], $wpRemoteRetrieveBody = true)
    {
        return new FakeWpService([
            'wpRemoteGet'          => $wpRemoteGet,
            'isWpError'            => ($wpRemoteGet instanceof \WP_Error) ? true : false,
            'wpRemoteRetrieveBody' => $wpRemoteRetrieveBody,
        ]);
    }
}
