<?php

namespace Municipio\Content\WpAutopContentGuard;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class WpAutopContentGuardTest extends TestCase {
    private WpAutopContentGuard $guard;

    protected function setUp(): void
    {
        $this->guard = new WpAutopContentGuard();
    }

    #[TestDox('locks content within <pre> tags by adding a specific class to prevent wpautop from modifying it')]
    public function testLock(): void {
        $input = '<div>Protected content</div>';
        $expectedOutput = '<pre class="wpautop-protected"><div>Protected content</div></pre>';
    
        static::assertEquals($expectedOutput, $this->guard->lock($input));
    }

    #[TestDox('unlocks content by removing the specific <pre> tags and class, allowing wpautop to modify it if necessary')]
    public function testUnlock(): void {
        $input = '<pre class="wpautop-protected"><div>Protected content</div></pre>';
        $expectedOutput = '<div>Protected content</div>';

        static::assertEquals($expectedOutput, $this->guard->unlock($input));
    }

    #[TestDox('unlocks multiple protected and nested sections within the content')]
    public function testUnlockWithMultipleProtectedSections(): void {
    
        $lines = [
            $this->guard->lock('<div>Protected content 1</div>'),
            $this->guard->lock('<p>Some other '.$this->guard->lock('content') . '</p>'),
            $this->guard->lock('<div>Protected content 2</div>'),
        ];

        $expectedOutput = <<<'HTML'
        <div>Protected content 1</div>
        <p>Some other content</p>
        <div>Protected content 2</div>
        HTML;

        static::assertEquals($expectedOutput, $this->guard->unlock(implode("\n", $lines)));
    }

    #[TestDox('unlocking content that does not have the specific <pre> tags and class should return the original content unchanged')]
    public function testUnlockWithUnprotectedContent(): void {
        $input = '<div>Unprotected content</div>';
        $expectedOutput = '<div>Unprotected content</div>';

        static::assertEquals($expectedOutput, $this->guard->unlock($input));
    }
}