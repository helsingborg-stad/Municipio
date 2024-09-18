<?php

namespace Municipio\TestUtils;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use stdClass;
use WP_Post;
use WP_Term;

/**
 * Class WpMockFactory.
 */
class WpMockFactory extends TestCase
{
    /**
     * Create a mock WP_Post object.
     *
     * @param array $args
     */
    public static function createWpPost(array $args = []): MockObject|WP_Post
    {
        return self::buildMockWithArgs('WP_Post', $args);
    }

    /**
     * Create a mock WP_Term object.
     *
     * @param array $args
     */
    public static function createWpTerm(array $args = []): MockObject|WP_Term
    {
        return self::buildMockWithArgs('WP_Term', $args);
    }

    /**
     * Build a mock object with arguments.
     *
     * @param string $className
     */
    private static function buildMockWithArgs(string $className, array $args): MockObject
    {
        $testCase = self::getTestCaseInstance();
        $mock     = $testCase->getMockBuilder(stdClass::class)->setMockClassName($className)->getMock();

        foreach ($args as $key => $value) {
            $mock->{$key} = $value;
        }

        return $mock;
    }

    /**
     * Get a test case instance.
     *
     * @return TestCase
     */
    private static function getTestCaseInstance(): TestCase
    {
        return new class extends TestCase {
        };
    }
}
