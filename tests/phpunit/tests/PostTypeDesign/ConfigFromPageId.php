<?php

namespace Municipio\Tests;

use WP_Mock;
use WP_Mock\Tools\TestCase;
use Mockery;
use Municipio\PostTypeDesign\ConfigFromPageId;
use WP_Error;

class ConfigFromPageIdTest extends TestCase
{
    // public function testGetReturnsNullIfApiResponseFails()
    // {
    //     $designId = '123';
    //     $configFromPageId = new ConfigFromPageId($designId);
        
    //     // Mockery::mock(\WP_Error::class);
    //     Mockery::mock(\WP_Error::class);


    //     WP_Mock::userFunction('wp_remote_get')
    //         ->andReturn(new WP_Error());
        
    //     $result = $configFromPageId->get();
    //     $this->assertEquals(null, $result);
    // }

    public function testGetReturnsTwoValuesWhenNotFailing()
    {
        $configFromPageId = new ConfigFromPageId('test');
        
        

        WP_Mock::userFunction('wp_remote_get');
        WP_Mock::userFunction('wp_remote_retrieve_body')
        ->andReturn(json_encode([]));
        
        $result = $configFromPageId->get();
        $this->assertCount(2, $result);
    }


    public function testGetReturnsModsAndCss()
    {
        $configFromPageId = new ConfigFromPageId('test');
        
        $array = ['mods' => 'modsValue', 'keyToBeRemoved' => 'value', 'css' => 'cssValue'];
        WP_Mock::userFunction('wp_remote_get');
        WP_Mock::userFunction('wp_remote_retrieve_body')
        ->andReturn(json_encode($array));
        
        $result = $configFromPageId->get();
        $this->assertCount(2, $result);
        $this->assertEquals($array['mods'], $result[0]);
        $this->assertEquals($array['css'], $result[1]);
    }
}