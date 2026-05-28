<?php

namespace Municipio\Upgrade\V41;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class DecorateDesignTokensTest extends TestCase
{
    #[Testdox('returns the tokens unchanged if no decoration is needed')]
    public function testReturnsUnchangedTokens(): void
    {
        $tokens = ['token' => ['--color--primary' => '#ff0000']];

        $decoratedTokens = (new DecorateDesignTokens())->decorate($tokens);

        $this->assertSame($tokens, $decoratedTokens);
    }

    #[TestDox('decorates the container width token with px')]
    public function testDecoratesContainerWidth(): void
    {
        $tokens = ['token' => ['--container-width' => '1200']];

        $decoratedTokens = (new DecorateDesignTokens())->decorate($tokens);

        $this->assertSame('1200px', $decoratedTokens['token']['--container-width']);
    }

    #[TestDox('converts border radius from a 0-8 scale to a 0-1 scale')]
    public function testConvertsBorderRadius(): void
    {
        $tokens = ['token' => ['--border-radius' => '4']];

        $decoratedTokens = (new DecorateDesignTokens())->decorate($tokens);

        $this->assertSame(0.5, $decoratedTokens['token']['--border-radius']);
    }

    #[TestDox('converts border width from a 0-8 scale to a 0-1 scale')]
    public function testConvertsBorderWidth(): void
    {
        $tokens = ['token' => ['--border-width' => '2']];

        $decoratedTokens = (new DecorateDesignTokens())->decorate($tokens);

        $this->assertSame(0.25, $decoratedTokens['token']['--border-width']);
    }

    #[TestDox('converts field border radius from a 0-8 scale to a 0-2 scale')]
    public function testConvertsFieldBorderRadius(): void
    {
        $tokens = ['component' => ['__general__' => ['field' => ['--c-field--border-radius' => '4']]]];

        $decoratedTokens = (new DecorateDesignTokens())->decorate($tokens);

        $this->assertSame(1, $decoratedTokens['component']['__general__']['field']['--c-field--border-radius']);
    }

    #[TestDox('converts card border width from a 0-8 scale to a 0-1 scale')]
    public function testConvertsCardBorderWidth(): void
    {
        $tokens = ['component' => ['__general__' => ['card' => ['--c-card--border-width' => '2']]]];

        $decoratedTokens = (new DecorateDesignTokens())->decorate($tokens);

        $this->assertSame(0.25, $decoratedTokens['component']['__general__']['card']['--c-card--border-width']);
    }

    #[TestDox('converts header logotype height from a 3-20 scale to the logotype multiplier')]
    public function testConvertsHeaderLogotypeHeightMultiplier(): void
    {
        $tokens = ['component' => ['__general__' => ['header' => ['--c-header--logotype-height-multiplier' => '3']]]];

        $decoratedTokens = (new DecorateDesignTokens())->decorate($tokens);

        $this->assertSame(0.5, $decoratedTokens['component']['__general__']['header']['--c-header--logotype-height-multiplier']);
    }

    #[TestDox('converts footer logotype height from a 3-12 scale to the home link height multiplier')]
    public function testConvertsFooterHomeLinkHeightMultiplier(): void
    {
        $tokens = ['component' => ['__general__' => ['footer' => ['--c-footer--home-link-height-multiplier' => '3']]]];

        $decoratedTokens = (new DecorateDesignTokens())->decorate($tokens);

        $this->assertSame(0.5, $decoratedTokens['component']['__general__']['footer']['--c-footer--home-link-height-multiplier']);
    }

    #[TestDox('converts --space from a 0-12 scale to a 0-2 scale')]
    public function testConvertsSpace(): void
    {
        $tokens = ['token' => ['--space' => '12']];

        $decoratedTokens = (new DecorateDesignTokens())->decorate($tokens);

        $this->assertSame(2.0, $decoratedTokens['token']['--space']);
    }

    #[TestDox('converts --outer-space from a 0-12 scale to a 0-3 scale')]
    public function testConvertsOuterSpace(): void
    {
        $tokens = ['token' => ['--outer-space' => '12']];

        $decoratedTokens = (new DecorateDesignTokens())->decorate($tokens);

        $this->assertSame(3.0, $decoratedTokens['token']['--outer-space']);
    }
}
