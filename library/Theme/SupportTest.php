<?php

namespace Municipio\Theme;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

function add_action(...$args): void
{
    SupportHookRecorder::$addActionCalls[] = $args;
}

function remove_action(...$args): void
{
    SupportHookRecorder::$removeActionCalls[] = $args;
}

function add_filter(...$args): void
{
    SupportHookRecorder::$addFilterCalls[] = $args;
}

function remove_filter(...$args): void
{
    SupportHookRecorder::$removeFilterCalls[] = $args;
}

function is_admin(): bool
{
    return false;
}

class SupportHookRecorder
{
    public static array $addActionCalls = [];
    public static array $removeActionCalls = [];
    public static array $addFilterCalls = [];
    public static array $removeFilterCalls = [];

    public static function reset(): void
    {
        self::$addActionCalls    = [];
        self::$removeActionCalls = [];
        self::$addFilterCalls    = [];
        self::$removeFilterCalls = [];
    }
}

class SupportTest extends TestCase
{
    protected function setUp(): void
    {
        SupportHookRecorder::reset();
    }

    #[TestDox('constructor removes WordPress shortlink output callbacks')]
    public function testConstructorRemovesWordPressShortlinkOutputCallbacks(): void
    {
        new Support();

        $this->assertContains(
            ['wp_head', 'wp_shortlink_wp_head', 10],
            SupportHookRecorder::$removeActionCalls,
        );
        $this->assertContains(
            ['template_redirect', 'wp_shortlink_header', 11],
            SupportHookRecorder::$removeActionCalls,
        );
    }
}
