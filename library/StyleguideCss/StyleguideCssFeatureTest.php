<?php

namespace Municipio\StyleguideCss;

use Municipio\StyleguideCss\CssVariables\CssVariable;
use Municipio\StyleguideCss\CssVariables\CssVariableInterface;
use Municipio\StyleguideCss\CssVariables\CssVariablesCollection;
use Municipio\StyleguideCss\CssVariables\CssVariablesCollectionInterface;
use Municipio\StyleguideCss\CssVariables\NullCssVariablesCollection;
use Municipio\StyleguideCss\ThemeSettingsMapper\ThemeSettingsMapper;
use Municipio\StyleguideCss\ThemeSettingsMapper\ThemeSettingsMapperInterface;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Contracts\AddAction;
use WpService\Contracts\GetThemeMods;

class StyleguideCssFeatureTest extends TestCase
{
    #[TestDox('can be instantiated')]
    public function testCanBeInstantiated(): void
    {
        $feature = new StyleguideCssFeature(self::createWpService());
        $this->assertInstanceOf(StyleguideCssFeature::class, $feature);
    }

    #[TestDox('attaches to the wp_head action')]
    public function testAttachesToWpHead(): void
    {
        $wpService = self::createWpService();
        $feature = new StyleguideCssFeature($wpService);
        $feature->addHooks();

        $addActionCalls = $wpService->addActionCalls;
        $wpHeadAction = array_filter($addActionCalls, fn($call) => $call['hookName'] === 'wp_head');

        // Assert wp_head action was added
        $this->assertNotEmpty($wpHeadAction, 'wp_head action was not added');
    }

    #[TestDox('outputs style tag in wp_head')]
    public function testOutputsStyleguideCssInWpHead(): void
    {
        $wpService = self::createWpService();
        $output = self::getFeatureOutput($wpService);
        $this->assertStringContainsString('<style', $output, 'Style tag was not output in wp_head');
    }

    #[TestDox('outputs styles in theme layer')]
    public function testOutputsStylesInThemeLayer(): void
    {
        $wpService = self::createWpService();
        $output = self::getFeatureOutput($wpService);
        // Here you can add more specific assertions based on the actual CSS you expect to be output
        $this->assertStringContainsString('@layer theme', $output, 'Expected CSS was not found in the output');
    }

    #[TestDox('outputs css variables if provided')]
    public function testOutputsCssVariablesIfProvided(): void
    {
        $wpService = self::createWpService();
        $cssVariables = [new CssVariable('--color--primary', '#FF0000')];
        $themeSettingsMapper = new class($cssVariables) implements ThemeSettingsMapperInterface {
            public function __construct(
                private array $cssVariables,
            ) {}

            public function map(array $themeMods): array
            {
                return $this->cssVariables;
            }
        };

        $output = self::getFeatureOutput($wpService, $themeSettingsMapper);

        $this->assertStringContainsString('--color--primary: #FF0000', $output, 'Expected CSS variable was not found in the output');
    }

    private static function getFeatureOutput(AddAction&GetThemeMods $wpService, ThemeSettingsMapperInterface $themeSettingsMapper = new ThemeSettingsMapper()): string
    {
        $feature = new StyleguideCssFeature($wpService, $themeSettingsMapper);
        $feature->addHooks();

        $addActionCalls = $wpService->addActionCalls;
        $wpHeadAction = array_filter($addActionCalls, fn($call) => $call['hookName'] === 'wp_head');
        $callback = $wpHeadAction[0]['callback'] ?? null;

        ob_start();
        $callback();
        return ob_get_clean();
    }

    private static function createWpService(): AddAction&GetThemeMods
    {
        return new class implements AddAction, GetThemeMods {
            public array $addActionCalls = [];

            public function addAction(string $hookName, callable $callback, int $priority = 10, int $acceptedArgs = 1): true
            {
                $this->addActionCalls[] = [
                    'hookName' => $hookName,
                    'callback' => $callback,
                    'priority' => $priority,
                    'acceptedArgs' => $acceptedArgs,
                ];

                return true;
            }

            public function getThemeMods(): array
            {
                return [];
            }
        };
    }
}
