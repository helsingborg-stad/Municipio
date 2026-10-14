<?php

declare(strict_types=1);

namespace Municipio\Controller;

use Municipio\PostObject\PostObjectInterface;
use PHPUnit\Framework\TestCase;

final class SingularJobPostingTest extends TestCase
{
    public function testPopulatesEthicsPoliciesFromHiringOrganizationRows(): void
    {
        $controller = $this->createController([
            ['ethicsPolicy' => 'https://example.com/policy-one'],
            ['name' => 'Organization without a policy'],
            ['ethicsPolicy' => 'https://example.com/policy-two'],
        ]);

        static::assertSame([
            'https://example.com/policy-one',
            'https://example.com/policy-two',
        ], $controller->data['ethicsPolicies']);
    }

    public function testLeavesEthicsPoliciesEmptyWhenHiringOrganizationsAreMissing(): void
    {
        $controller = $this->createController(null);

        static::assertSame([], $controller->data['ethicsPolicies']);
    }

    private function createController(mixed $hiringOrganizations): SingularJobPosting
    {
        $controller = (new \ReflectionClass(SingularJobPosting::class))->newInstanceWithoutConstructor();
        $post = $this->createMock(PostObjectInterface::class);
        $post->method('getSchemaProperty')
            ->with('hiringOrganization')
            ->willReturn($hiringOrganizations);

        (new \ReflectionProperty(Singular::class, 'post'))->setValue($controller, $post);
        (new \ReflectionMethod(SingularJobPosting::class, 'populateEthicsPolicies'))->invoke($controller);

        return $controller;
    }
}
