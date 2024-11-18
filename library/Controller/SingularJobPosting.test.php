<?php

namespace Municipio\Controller;

use PHPUnit\Framework\TestCase;
use Municipio\Controller\SingularJobPosting;
use PHPUnit\Framework\MockObject\MockObject;

class SingularJobPostingTest extends TestCase
{
    protected SingularJobPosting $controller;

    /**
     * @testdox getValidThroughListItemValue() return contains 'expired' if validThrough was yesterday
     */
    public function testValidThroughContainsExpired()
    {
        $controller               = $this->getController();
        $validThrough             = date('Y-m-d', strtotime('-1 day'));
        $controller->data['post'] = (object) [ 'schemaObject' => [ 'validThrough' => $validThrough ] ];

        $result = $controller->getValidThroughListItemValue();

        $this->assertStringContainsString('expired', $result);
    }

    /**
     * @testdox getValidThroughListItemValue() return contains 'today' if validThrough is today
     */
    public function testValidThroughContainsToday()
    {
        $controller               = $this->getController();
        $validThrough             = date('Y-m-d');
        $controller->data['post'] = (object) [ 'schemaObject' => [ 'validThrough' => $validThrough ] ];

        $result = $controller->getValidThroughListItemValue();

        $this->assertStringContainsString('today', $result);
    }

    /**
     * @testdox getValidThroughListItemValue() return contains 'tomorrow' if validThrough is tomorrow
     */
    public function testValidThroughContainsTomorrow()
    {
        $controller               = $this->getController();
        $validThrough             = date('Y-m-d', strtotime('+1 day'));
        $controller->data['post'] = (object) [ 'schemaObject' => [ 'validThrough' => $validThrough ] ];

        $result = $controller->getValidThroughListItemValue();

        $this->assertStringContainsString('tomorrow', $result);
    }

    /**
     * @testdox getValidThroughListItemValue() return contains 'days' if validThrough is in the future
     */
    public function testValidThroughContainsDays()
    {
        $controller               = $this->getController();
        $validThrough             = date('Y-m-d', strtotime('+2 days'));
        $controller->data['post'] = (object) [ 'schemaObject' => [ 'validThrough' => $validThrough ] ];

        $result = $controller->getValidThroughListItemValue();

        $this->assertStringContainsString('2 days', $result);
    }

    private function getController(): MockObject|SingularJobPosting
    {
        $controller = $this->getMockBuilder(SingularJobPosting::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['init'])
            ->getMock();

        $controller->data['lang'] = (object)[
            'days'     => 'days',
            'today'    => 'today',
            'tomorrow' => 'tomorrow',
            'expired'  => 'expired'
        ];

        return $controller;
    }
}
