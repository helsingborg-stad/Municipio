<?php

namespace Municipio;

use AcfService\Implementations\FakeAcfService;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class UpgradeTest extends TestCase
{
    #[TestDox('v_33 migrates post type schema settings')]
    public function testV33()
    {
        $wpService  = new FakeWpService(['getPostTypes' => ['test_post_type'], 'addAction' => true]);
        $acfService = new FakeAcfService(['getField' => 'Thing', 'updateField' => true]);
        $upgrade    = new Upgrade($wpService, $acfService);

        $upgrade->v_33((object)[]);

        $this->assertEquals('schema', $acfService->methodCalls['getField'][0][0]);
        $this->assertEquals('test_post_type_options', $acfService->methodCalls['getField'][0][1]);
        $this->assertEquals('post_type_schema_types', $acfService->methodCalls['updateField'][0][0]);
        $this->assertEquals([['post_type' => 'test_post_type', 'schema_type' => 'Thing']], $acfService->methodCalls['updateField'][0][1]);
    }
}
