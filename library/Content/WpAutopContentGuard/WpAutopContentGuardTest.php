<?php

declare(strict_types=1);


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

    #[TestDox('unlocks empty localized content without errors')]
    public function testUnlockWithEmptyLocalizedContent(): void {
        $input = $this->guard->lock('   ');
        $expectedOutput = '   ';

        static::assertEquals($expectedOutput, $this->guard->unlock($input));
    }

    #[TestDox('unlocks protected content embedded in a large full-page HTML document without PCRE backtrack-limit errors')]
    public function testUnlockInsideLargeDocument(): void {
        $filler      = str_repeat('<p>' . str_repeat('a', 100) . '</p>', 2000); // ~250 KB of surrounding HTML
        $protected   = $this->guard->lock('<div class="c-acceptance"><template><iframe src="https://youtube.com/embed/test"></iframe></template></div>');
        $input       = '<html><body>' . $filler . $protected . $filler . '</body></html>';
        $output      = $this->guard->unlock($input);

        static::assertStringNotContainsString('wpautop-protected', $output);
        static::assertStringContainsString('<div class="c-acceptance">', $output);
    }
}