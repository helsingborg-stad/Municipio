<?php

declare(strict_types=1);

namespace Modularity\Helper;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class ModuleInstanceIdTest extends TestCase
{
    #[TestDox('fromModule() builds the prefix from the module post type and id')]
    public function testFromModuleBuildsPrefixFromPostTypeAndId(): void
    {
        static::assertSame('mod-text-123-', ModuleInstanceId::fromModule($this->createModule())->getPrefix());
    }

    #[TestDox('getPlaceholder() is the same for every instance of the same module')]
    public function testPlaceholderIsStableForTheSameModule(): void
    {
        $first = ModuleInstanceId::fromModule($this->createModule());
        $second = ModuleInstanceId::fromModule($this->createModule());

        static::assertSame($first->getPlaceholder(), $second->getPlaceholder());
        static::assertStringStartsWith('mod-text-123-', $first->getPlaceholder());
    }

    #[TestDox('replacePlaceholder() replaces every placeholder in the markup with the given instance id')]
    public function testReplacePlaceholderReplacesAllOccurrences(): void
    {
        $instanceId = ModuleInstanceId::fromModule($this->createModule());
        $markup = sprintf('<div id="%1$s"><a href="#%1$s">Link</a></div>', $instanceId->getPlaceholder());

        static::assertSame(
            '<div id="mod-text-123-7"><a href="#mod-text-123-7">Link</a></div>',
            $instanceId->replacePlaceholder($markup, 'mod-text-123-7'),
        );
    }

    #[TestDox('replacePlaceholder() leaves placeholders of other modules untouched')]
    public function testReplacePlaceholderLeavesOtherModulesUntouched(): void
    {
        $instanceId = ModuleInstanceId::fromModule($this->createModule());
        $otherPlaceholder = (new ModuleInstanceId('mod-posts-456-'))->getPlaceholder();

        static::assertSame($otherPlaceholder, $instanceId->replacePlaceholder($otherPlaceholder, 'mod-text-123-7'));
    }

    private function createModule(): \WP_Post
    {
        $module = new \WP_Post((object) []);
        $module->ID = 123;
        $module->post_type = 'mod-text';

        return $module;
    }
}
