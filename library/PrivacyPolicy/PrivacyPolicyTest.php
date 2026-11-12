<?php

declare(strict_types=1);

namespace Municipio\PrivacyPolicy;

use AcfService\Implementations\FakeAcfService;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class PrivacyPolicyTest extends TestCase
{
    #[TestDox('It registers the policy route after inherited common field values are available.')]
    public function testAddHooksRegistersFrontendPageAfterCommonFieldGroups(): void
    {
        $wpService = new FakeWpService([
            'addAction' => true,
            'addFilter' => true,
        ]);
        $instance = new PrivacyPolicy($wpService, new FakeAcfService());

        $instance->addHooks();

        $frontendPageAction = array_values(array_filter(
            $wpService->methodCalls['addAction'],
            static fn(array $action): bool => $action[1] === [$instance, 'registerFrontendPage'],
        ));

        static::assertCount(1, $frontendPageAction);
        static::assertSame('init', $frontendPageAction[0][0]);
        static::assertSame(20, $frontendPageAction[0][2]);
    }

    #[TestDox('It does not register the route when the privacy policy is disabled.')]
    public function testRegisterFrontendPageDoesNothingWhenDisabled(): void
    {
        $wpService = new FakeWpService([
            'addRewriteRule' => true,
            'addFilter'      => true,
            'addAction'      => true,
            '__'             => fn($text) => $text,
        ]);
        $instance = new PrivacyPolicy($wpService, new FakeAcfService(['getField' => false]));

        $instance->registerFrontendPage();

        static::assertArrayNotHasKey('addRewriteRule', $wpService->methodCalls);
    }

    #[TestDox('It registers the route when the privacy policy is enabled.')]
    public function testRegisterFrontendPageRegistersRewriteRuleWhenEnabled(): void
    {
        $wpService = new FakeWpService([
            'addRewriteRule' => true,
            'addFilter'      => true,
            'addAction'      => true,
            '__'             => fn($text) => $text,
        ]);
        $instance = new PrivacyPolicy($wpService, new FakeAcfService(['getField' => true]));

        $instance->registerFrontendPage();

        static::assertSame(
            ['^privacy-policy/?$', 'index.php?privacy_policy=1', 'top'],
            $wpService->methodCalls['addRewriteRule'][0]
        );
    }

    #[TestDox('It replaces the url placeholder in the view policy button.')]
    public function testLoadPrivacyPolicyUrlFieldReplacesPlaceholder(): void
    {
        $wpService = new FakeWpService([
            '__'      => fn($text) => $text,
            'homeUrl' => fn($path = '') => 'https://example.com/' . $path,
        ]);
        $instance = new PrivacyPolicy($wpService, new FakeAcfService());

        $field = $instance->loadPrivacyPolicyUrlField(['message' => '<a href="{{privacy_policy_page_url}}">View</a>']);

        static::assertSame('<a href="https://example.com/privacy-policy">View</a>', $field['message']);
    }
}
