<?php

namespace Municipio\PostsList\ViewCallableProviders;

use Municipio\PostsList\Config\AppearanceConfig\DateFormat;
use Municipio\PostsList\Config\AppearanceConfig\DefaultAppearanceConfig;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class ShowDateBadgeTest extends TestCase
{
    #[TestDox('returns true if any appearance config dateFormat is DATE_BADGE')]
    public function testReturnsTrueIfAnyPostHasDateBadgeFormat(): void
    {
        $appearanceConfig = new class extends DefaultAppearanceConfig {
            public function getDateFormat(): DateFormat
            {
                return DateFormat::DATE_BADGE;
            }
        };

        $showDateBadgeUtility = new ShowDateBadge($appearanceConfig);

        $this->assertTrue($showDateBadgeUtility->getCallable()());
    }

    #[TestDox('returns false if no appearance config dateFormat is DATE_BADGE')]
    public function testReturnsFalseIfNoPostHasDateBadgeFormat(): void
    {
        $appearanceConfig = new class extends DefaultAppearanceConfig {
            public function getDateFormat(): DateFormat
            {
                return DateFormat::DATE;
            }
        };

        $showDateBadgeUtility = new ShowDateBadge($appearanceConfig);

        $this->assertFalse($showDateBadgeUtility->getCallable()());
    }
}
