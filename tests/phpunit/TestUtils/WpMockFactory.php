<?php

namespace Municipio\TestUtils;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use stdClass;
use WP_Comment_Query;
use WP_Error;
use WP_Post;
use WP_Term;
use WP_Taxonomy;
use wpdb;

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
     * Create a mock WP_Error object.
     *
     * @param array $args
     */
    public static function createWpError(array $args = []): MockObject|WP_Error
    {
        return self::buildMockWithArgs('WP_Error', $args);
    }

    /**
     * Create a mock WP_Error object.
     *
     * @param array $args
     */
    public static function createWpCommentQuery(array $args = []): MockObject|WP_Comment_Query
    {
        return self::buildMockWithArgs('WP_Comment_Query', $args);
    }

    /**
     * Create a mock WP_Taxonomy object.
     *
     * @param array $args
     */
    public static function createWpTaxonomy(array $args = []): MockObject|WP_Taxonomy
    {
        return self::buildMockWithArgs('WP_Taxonomy', $args);
    }

    /**
     * Create a mock wpdb object.
     *
     * @param array $args
     */
    public static function createWpdb(array $args = []): MockObject|wpdb
    {
        return self::buildMockWithArgs('wpdb', $args);
    }

    /**
     * Build a mock object with arguments.
     *
     * @param string $className
     */
    private static function buildMockWithArgs(string $className, array $args): MockObject
    {
        $testCase = self::getTestCaseInstance();
        $mock     = $testCase->getMockBuilder(stdClass::class)->setMockClassName($className);

        $methods = array_map(
            fn($key) => is_callable($args[$key]) ? $key : null,
            array_keys($args)
        );

        $mock = $mock->addMethods(array_filter($methods))->getMock();

        foreach ($args as $key => $value) {
            if (is_callable($value)) {
                $mock->method($key)->willReturnCallback($value);
                continue;
            }

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
