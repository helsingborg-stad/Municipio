<?php

namespace Municipio\Tests;

use WP_Mock;
use WP_Mock\Tools\TestCase;
use Mockery;
use Municipio\PostTypeDesign\SaveDesigns;

class testSaveDesigns extends TestCase
{
    public function testSetDesignsSetupFilters()
    {
        $saveDesignsInstance = new SaveDesigns('name');
        WP_Mock::expectActionAdded('customize_save_after', [$saveDesignsInstance, 'storeDesign']);
        $saveDesignsInstance->addHooks();

        WP_Mock::assertActionsCalled();
    }

    public function testStoreDesignsReturnsIfNoPostTypes()
    {
        $saveDesignsInstance = new SaveDesigns('name');
        WP_Mock::userFunction('get_post_types', [
            'args' => [['public' => true], 'names'],
            'return' => []
        ]);

        WP_Mock::userFunction('get_option', [
            'times' => '0'
        ]);

        $saveDesignsInstance->storeDesigns();

        $this->assertTrue(true);
    }

    /**
     * @runInSeparateProcess
    */
    public function testTryUpdateOptionWithDesignDoesntUpdateOptionIfNoDesign()
    {
        $saveDesignsInstance = new SaveDesigns('name');
        $mock = Mockery::mock('overload:Municipio\PostTypeDesign\ConfigFromPageId');
        $mock->shouldReceive('get')->andReturn([[], null]);

        WP_Mock::userFunction('update_option', [
            'times' => '0'
        ]);
        
        $result = $saveDesignsInstance->tryUpdateOptionWithDesign('designId', [], 'postType');

        $this->assertTrue(true);
    }

    /**
     * @runInSeparateProcess
    */
    public function testTryUpdateOptionWithDesignUpdateOptionDesign()
    {
        $saveDesignsInstance = new SaveDesigns('name');
        $mock = Mockery::mock('overload:Municipio\PostTypeDesign\ConfigFromPageId');
        $mock->shouldReceive('get')->andReturn([['abc'], null]);

        WP_Mock::userFunction('update_option', [
            'times' => '1'
        ]);
        
        $result = $saveDesignsInstance->tryUpdateOptionWithDesign('designId', [], 'postType');

        $this->assertTrue(true);
    }
}